<?php

namespace App\Jobs;

use App\Models\Story;
use getID3;
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
        $this->handleOrMock();
        $getID3 = new getID3();
        $filepath = storage_path('app/public/audio/story-' . $this->story->id . '.mp3');
        $fileInfo = $getID3->analyze($filepath);
        $this->story->update([
            'voice_over_path' => 'audio/story-' . $this->story->id . '.mp3',
            'duration_in_seconds' => $fileInfo['playtime_seconds']
        ]);
    }

    protected function mock()
    {
        // Simply copy the file from the storage to the public folder
        Storage::disk('public')->put('audio/story-' . $this->story->id . '.mp3', file_get_contents(storage_path('mocks/story-mock.mp3')));
    }

    protected function shouldMock(): bool
    {
        return env('SHOULD_MOCK_STORY', false);
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
