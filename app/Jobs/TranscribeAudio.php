<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class TranscribeAudio implements ShouldQueue
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
        $transcript = app('replicate')->run('vaibhavs10/incredibly-fast-whisper:3ab86df6c8f54c11309d4d1f930ac292bad43ace52d10c80d87eb258b3c9f79c', [
            'audio' => env('NGROK_URL') . Storage::url($this->story->voice_over_path),
            'timestamp' => 'word',
        ]);
        $this->story->update([
            'voice_over_transcription' => $transcript,
        ]);
    }
}
