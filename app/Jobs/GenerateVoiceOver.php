<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateVoiceOver extends MockableJob implements ShouldQueue
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
        $this->story->update([
            'voice_over_path' => 'audio/story-' . $this->story->id . '.mp3'
        ]);
    }

    protected function mock()
    {
        // Simply copy the file from the storage to the public folder
        Storage::copy('mocks/story-mock.mp3', 'public/audio/story-' . $this->story->id . '.mp3');
    }

    protected function shouldMock(): bool
    {
        return true;
    }

    protected function execute()
    {
        $audioString = app('openai')->audio()->speech([
            'model' => 'tts-1',
            'input' => $this->story->content,
            'voice' => 'alloy',
        ]);
        Storage::disk('public')->put('audio/story-' . $this->story->id . '.mp3', $audioString);
    }
}
