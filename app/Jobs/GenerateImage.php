<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Story;
use App\Prompts\DescribeScene;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SabatinoMasala\Replicate\Replicate;

class GenerateImage implements ShouldQueue
{
    use Queueable, Batchable;

    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story,
        public Image $image,
    ){}

    /**
     * Execute the job.
     */
    public function handle(Replicate $replicate): void
    {
        $prompt = new DescribeScene($this->story->content, $this->image->paragraph, $this->story->creative_direction);
        $tokens = $replicate->run(config('models.llm'), [
            'prompt' => $prompt->get(),
        ]);
        $imagePrompt = collect($tokens)->implode('');
        $this->image->update([
            'prompt' => $imagePrompt,
        ]);
        $res = $replicate->run(config('models.diffuser'), [
            'prompt' => $imagePrompt,
            'aspect_ratio' => '9:16',
            'disable_safety_checker' => true,
        ]);
        $this->image->update([
            'image_path' => $res[0],
            'status' => 'COMPLETED'
        ]);
    }



    public function failed()
    {
        $this->image->update([
            'status' => 'FAILED',
        ]);
    }

}
