<?php

namespace App\Jobs;

use App\Models\Story;
use App\Prompts\Brainstorm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SabatinoMasala\Replicate\Replicate;

class BrainstormStoryTitle extends MockableJob implements ShouldQueue
{

    use Queueable;

    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story,
    ) {}

    protected function shouldMock(): bool
    {
        return env('SHOULD_MOCK_STORY', false);
    }

    public function execute(Replicate $replicate)
    {
        $prompt = new Brainstorm($this->story->language, $this->story->series);
        $prompt->addHistory([
            'The story about julius caesar'
        ]);
        $output = $replicate->run(config('models.llm'), [
            'prompt' => $prompt->get(),
            'max_tokens' => 1000,
        ]);
        $list = explode(PHP_EOL, collect($output)->join(''));
        return $list[array_rand($list)];
    }

    protected function mock()
    {
        return 'Veni Vidi Vici, the Conqueror\'s Cry';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->story->update([
            'status' => 'BRAINSTORM_START',
        ]);
        $this->story->update([
            'title' => $this->handleOrMock(),
            'status' => 'BRAINSTORM_END',
        ]);
    }

    public function failed()
    {
        $this->story->update([
            'status' => 'BRAINSTORM_FAILED',
        ]);
    }

}
