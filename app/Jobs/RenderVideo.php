<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;

class RenderVideo implements ShouldQueue
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
        $process = new Process([
            'yarn',
            'build',
            '--props=' . json_encode([
                'json' => 'http://faceless-laravel-example.test/api/story/' . $this->story->id,
            ]),
            '--output=' . base_path('storage/app/public/video/' . $this->story->id . '.mp4'),
            ], base_path('faceless'));
        $process->run();
        \Log::info($process->getOutput());
        \Log::info($process->getErrorOutput());
    }
}
