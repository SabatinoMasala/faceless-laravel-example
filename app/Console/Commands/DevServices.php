<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Sleep;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;

class DevServices extends Command implements SignalableCommandInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';
    public $shouldExit = false;

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

        $processes = [
            'horizon' => [
                'command' => ['php', 'artisan', 'horizon'],
                'style' => ['cyan', null, ['bold']],
            ],
            'ngrok' => [
                'command' => ['valet', 'share', '--domain=' . $ngrokUrl],
                'style' => ['green', null, ['bold']],
            ],
            'reverb' => [
                'command' => ['php', 'artisan', 'reverb:start', '--verbose', '--debug'],
                'style' => ['magenta', null, ['bold']],
            ],
        ];

        $processes = collect($processes)->mapWithKeys(function($input, $key) {
            $this->info('Starting ' . $key);
            $process = new Process($input['command']);
            $style = new OutputFormatterStyle(...$input['style']);
            $this->output->getFormatter()->setStyle($key, $style);
            $process->start();
            return [$key => $process];
        });

        while (true) {
            if ($this->shouldExit) {
                break;
            }
            $processes->each(function($process, $key) {
                if ($process->isRunning()) {
                    $output = $process->getIncrementalOutput();
                    $errorOutput = $process->getIncrementalErrorOutput();
                    if (!empty($output)) {
                        $output = explode(PHP_EOL, $output);
                        collect($output)->filter()->each(function($output) use ($key) {
                            $this->line("<$key>$key</$key>: " . trim($output));
                        });
                    }
                    if (!empty($errorOutput)) {
                        $this->error(trim($errorOutput));
                    }
                }
            });
            Sleep::for(1)->seconds();
        }

        $processes->each(function($process, $key) {
            if ($process->isRunning()) {
                $process->signal(SIGINT);
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
