<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateStory extends MockableJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story
    ){}

    protected function mock()
    {
        return 'As the sun dipped into the horizon, casting a golden glow over the battle-scarred landscape, Emperor Julius Caesar stood victorious atop a hill, gazing out at the sprawling city of Zela. The air was heavy with the scent of smoke and sweat, the sound of clashing steel and the cries of the fallen still echoing in his mind.\n\nWith a deep breath, he raised his arms to the sky and bellowed the words that would become his legend: \"Veni, Vidi, Vici!\" - I came, I saw, I conquered! The phrase thundered through the valleys, striking fear into the hearts of his enemies and inspiring his troops to greater heights.\n\nCaesar\'s campaign had been long and brutal, but with this triumph, he had secured a vital trade route and cemented his position as the greatest leader Rome had ever known. As the stars began to twinkle in the night sky, he knew that his name would be etched in history forever, synonymous with power, strategy, and unyielding ambition.\n\nWith a satisfied smile, Caesar turned to his loyal generals, his eyes aglow with the fire of conquest. \"We have won the day,\" he said, his voice low and gravelly. \"But tomorrow, we will win the world.\" And with that, the Roman Empire marched on, unstoppable and unyielding, with Caesar at its helm, forever changed by the conqueror\'s cry: Veni, Vidi, Vici.';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->story->update([
            'status' => 'STORY_START',
        ]);
        $this->story->update([
            'content' => $this->handleOrMock(),
            'status' => 'STORY_END',
        ]);
    }

    protected function shouldMock(): bool
    {
        return env('SHOULD_MOCK_STORY', false);
    }

    protected function execute()
    {
        $prompt = new \App\Prompts\GenerateStory($this->story->language, $this->story->title);
        $output = app('replicate')->run('meta/meta-llama-3.1-405b-instruct', [
            'prompt' => $prompt->get(),
            'max_tokens' => 1000,
        ]);
        return collect($output)->join('');
    }
}
