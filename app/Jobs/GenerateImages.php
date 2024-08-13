<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Story;
use App\Prompts\DescribeScene;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SabatinoMasala\Replicate\Replicate;
use Spatie\Fork\Fork;

class GenerateImages implements ShouldQueue
{
    use Queueable;

    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story
    ){}

    /**
     * Execute the job.
     */
    public function handle(Replicate $replicate): void
    {
        $this->story->update([
            'status' => 'IMAGES_START',
        ]);
        $this->story->images()->delete();
        collect($this->story->voice_over_chunks['groups'])->each(function($group) {
            $this->story->images()->create([
                'status' => 'PENDING',
                'paragraph' => $group['text']
            ]);
        });
        $callables = $this->story->images->take(1)->map(function(Image $image) use ($replicate) {
            return function () use ($image, $replicate) {
                $prompt = new DescribeScene($this->story->content, $image->paragraph, $this->story->creative_direction);
                $tokens = $replicate->run(config('models.llm'), [
                    'prompt' => $prompt->get(),
                ]);
                $imagePrompt = collect($tokens)->implode('');
                $image->update([
                    'prompt' => $imagePrompt,
                ]);
                $res = $replicate->run(config('models.diffuser'), [
                    'prompt' => $imagePrompt,
                    'aspect_ratio' => '9:16',
                    'disable_safety_checker' => true,
                ]);
                $image->update([
                    'image_path' => $res[0],
                    'status' => 'COMPLETED'
                ]);
            };
        });
        $results = Fork::new()
            ->run(...$callables);
        $this->story->update([
            'status' => 'IMAGES_END',
        ]);
    }



    public function failed()
    {
        $this->story->update([
            'status' => 'IMAGES_FAILED',
        ]);
    }

}
