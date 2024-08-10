<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateVoiceOver implements ShouldQueue
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
        $audioString = app('openai')->audio()->speech([
            'model' => 'tts-1',
            'input' => $this->story->content,
            'voice' => 'alloy',
        ]);
        Storage::disk('public')->put('audio/story-' . $this->story->id . '.mp3', $audioString);
        $this->story->update([
            'voice_over_path' => 'audio/story-' . $this->story->id . '.mp3'
        ]);
    }
}
