<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Sleep;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;

class DevServices extends Command implements SignalableCommandInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';

    public $shouldExit = false;
    protected $queueWorkers = [];
    protected $numQueueWorkers = 2;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!env('NGROK_URL')) {
            $ngrokUrl = 'https://' . substr(md5(rand()), 0, 12) . '.ngrok.app';
            $this->setEnvironmentValue('NGROK_URL', $ngrokUrl);
        } else {
            $ngrokUrl = env('NGROK_URL');
        }

        $ngrokUrl = str_replace('https://', '', $ngrokUrl);

        for ($i = 0; $i < $this->numQueueWorkers; $i++) {
            $this->info('Starting queue worker #' . $i);
            $queueWorker = new Process(['php', 'artisan', 'queue:work']);
            $queueWorker->start();
            $this->info('Queue worker started.');
            $this->queueWorkers[] = $queueWorker;
        }

        $this->info('Starting queue ngrok...');
        $ngrok = new Process(['valet', 'share', '--domain=' . $ngrokUrl]);
        $ngrok->start();
        $this->info('Ngrok started on URL: https://' . $ngrokUrl);

        $this->info('Starting reverb...');
        $reverb = new Process(['php', 'artisan', 'reverb:start']);
        $reverb->start();
        $this->info('reverb started');

        while (true) {
            if ($this->shouldExit) {
                break;
            }
            Sleep::for(3)->seconds();
        }

        if ($reverb->isRunning()) {
            $reverb->signal(SIGINT);
        }
        if ($ngrok->isRunning()) {
            $ngrok->signal(SIGINT);
        }
        collect($this->queueWorkers)->each(function($worker) {
            if ($worker->isRunning()) {
                $worker->signal(SIGINT);
            }
        });
    }

    public function getSubscribedSignals(): array
    {
        return [
            SIGINT,
            SIGTERM,
        ];
    }

    public function handleSignal(int $signal, false|int $previousExitCode = 0): int|false
    {
        $this->shouldExit = true;
        return false;
    }

    protected function setEnvironmentValue($key, $value)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            // Read the .env file content
            $env = File::get($path);

            // Replace or append the key=value pair
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }

            // Save the updated content back to the .env file
            File::put($path, $env);
        }
    }

}
