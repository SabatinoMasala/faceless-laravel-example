<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;

class RenderVideo implements ShouldQueue
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
            'status' => 'VIDEO_START',
        ]);
        $process = new Process([
            'yarn',
            'build',
            '--props=' . json_encode([
                'json' => 'http://faceless-laravel-example.test/api/stories/' . $this->story->id,
            ]),
            '--output=' . base_path('storage/app/public/video/' . $this->story->id . '.mp4'),
            ], base_path('faceless'));
        $process->run();
        if ($process->isSuccessful()) {
            $this->story->update([
                'video_path' => 'video/' . $this->story->id . '.mp4',
                'status' => 'COMPLETED',
            ]);
        } else {
            \Log::info($process->getOutput());
            \Log::info($process->getErrorOutput());
            $this->story->update([
                'status' => 'VIDEO_ERROR',
            ]);
            throw new \Exception('Failed to render video');
        }
    }

    public function failed()
    {
        $this->story->update([
            'status' => 'VIDEO_FAILED',
        ]);
    }

}
