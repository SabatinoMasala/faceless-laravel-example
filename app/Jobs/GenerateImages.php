<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Story;
use App\Prompts\DescribeScene;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Fork\Fork;

class GenerateImages implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        collect($this->story->voice_over_chunks['groups'])->each(function($group) {
            $this->story->images()->create([
                'status' => 'PENDING',
                'paragraph' => $group['text']
            ]);
        });
        $callables = $this->story->images->map(function(Image $image) {
            return function () use ($image) {
                $prompt = new DescribeScene($this->story->content, $image->paragraph, $this->story->creative_direction);
                $tokens = app('replicate')->run('meta/meta-llama-3.1-405b-instruct', [
                    'prompt' => $prompt->get(),
                ]);
                $imagePrompt = collect($tokens)->implode('');
                $image->update([
                    'prompt' => $imagePrompt,
                ]);
                $res = app('replicate')->run('black-forest-labs/flux-schnell', [
                    'prompt' => $imagePrompt,
                    'aspect_ratio' => '9:16',
                ]);
                $image->update([
                    'image_path' => $res[0],
                ]);
            };
        });
        $results = Fork::new()
            ->run(...$callables);
    }
}
