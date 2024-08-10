<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateStory implements ShouldQueue
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
        $prompt = new \App\Prompts\GenerateStory($this->story->language, $this->story->title);
        $output = app('replicate')->run('meta/meta-llama-3.1-405b-instruct', [
            'prompt' => $prompt->get(),
            'max_tokens' => 1000,
        ]);
        $this->story->update([
            'content' => collect($output)->join('')
        ]);
    }
}
