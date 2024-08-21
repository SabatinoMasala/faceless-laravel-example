<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use SabatinoMasala\Replicate\Replicate;

class TranscribeAudio extends MockableJob implements ShouldQueue
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
    public function handle(): void
    {
        $this->story->update([
            'status' => 'TRANSCRIBE_START',
        ]);
        $this->story->update([
            'voice_over_transcription' => $this->handleOrMock(),
            'status' => 'TRANSCRIBE_END',
        ]);
    }

    protected function mock()
    {
        return json_decode(file_get_contents(storage_path('mocks/transcript.json')), true);
    }

    protected function shouldMock(): bool
    {
        return env('SHOULD_MOCK_STORY', false);
    }

    public function execute(Replicate $replicate)
    {
        if (config('app.env') === 'local' && env('NGROK_URL') !== null) {
            $audio = env('NGROK_URL') . Storage::url($this->story->voice_over_path);
        } else {
            $audio = url(Storage::url($this->story->voice_over_path));
        }
        return $replicate->run('vaibhavs10/incredibly-fast-whisper:3ab86df6c8f54c11309d4d1f930ac292bad43ace52d10c80d87eb258b3c9f79c', [
            'audio' => $audio,
            'timestamp' => 'word',
        ]);
    }

    public function failed()
    {
        $this->story->update([
            'status' => 'TRANSCRIBE_FAILED',
        ]);
    }

}
