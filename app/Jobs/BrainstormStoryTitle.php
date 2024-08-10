<?php

namespace App\Jobs;

use App\Models\Story;
use App\Prompts\Brainstorm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SabatinoMasala\Replicate\Replicate;

class BrainstormStoryTitle implements ShouldQueue
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
        $prompt = new Brainstorm($this->story->language, $this->story->series);
        $prompt->addHistory([
            'The story about julius caesar'
        ]);
        $output = app('replicate')->run('meta/meta-llama-3.1-405b-instruct', [
            'prompt' => $prompt->get(),
            'max_tokens' => 1000,
        ]);
        $list = explode(PHP_EOL, collect($output)->join(''));
        $randomIdea = $list[array_rand($list)];
        $this->story->update([
            'title' => $randomIdea
        ]);
    }
}
