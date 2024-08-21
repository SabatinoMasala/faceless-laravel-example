<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Support\Facades\Storage;
use Remotion\LambdaPhp\PHPClient;
use Remotion\LambdaPhp\RenderParams;
use Aws\Credentials\CredentialProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;

class RenderVideo implements ShouldQueue
{

    use Queueable;

    public $timeout = 1200;
    public $tries = 1;

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
        if (env('NGROK_URL')) {
            $json = env('NGROK_URL') . '/api/stories/' . $this->story->id;
        } else {
            $json = url('/api/stories/' . $this->story->id);
        }
        if (env('REMOTION_APP_SERVE_URL')) {
            // Do remote rendering
            $this->renderRemote($json);
        } else {
            // Do local rendering
            $this->renderLocally($json);
        }
    }

    protected function renderRemote($json)
    {
        $region = env('REMOTION_APP_REGION');
        $functionName = env('REMOTION_APP_FUNCTION_NAME');
        $serveUrl = env('REMOTION_APP_SERVE_URL');
        $provider = CredentialProvider::defaultProvider();
        $client = new PHPClient($region, $serveUrl, $functionName, $provider);
        $params = new RenderParams();
        $params->setComposition('FacelessVideo');
        $params->setInputProps(['json' => $json, 'fps' => 30]);
        $params->setImageFormat('png');
        $renderResponse = $client->renderMediaOnLambda($params);
        $renderId = $renderResponse->renderId;
        $bucketName = $renderResponse->bucketName;

        $renderProgressResponse = $client->getRenderProgress($renderId, $bucketName);
        \Log::info($renderResponse->renderId);

        while (!$renderProgressResponse->done) {
            $renderProgress = $renderProgressResponse->overallProgress;
            \Log::info('progress: ' . ($renderProgress * 100) . "%\n");
            sleep(1);
            $renderProgressResponse = $client->getRenderProgress($renderId, $bucketName);
        }
        if (!$renderProgressResponse->fatalErrorEncountered) {
            Storage::put('public/video/' . $this->story->id . '.mp4', file_get_contents($renderProgressResponse->outputFile));
            $this->story->update([
                'video_path' => 'video/' . $this->story->id . '.mp4',
                'status' => 'COMPLETED',
            ]);
        } else {
            \Log::error('Failed to render video');
            $this->story->update([
                'status' => 'VIDEO_ERROR',
            ]);
            throw new \Exception('Failed to render video');
        }
    }

    protected function renderLocally($json)
    {
        $process = new Process([
            'yarn',
            'build',
            '--props=' . json_encode([
                'json' => $json,
                'fps' => 1
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
