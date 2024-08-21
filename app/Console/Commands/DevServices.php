<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
    protected $description = 'Starts dev services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $processes = [
            'share' => [
                'command' => ['php', 'artisan', 'share'],
                'style' => ['green', null, ['bold']],
                'logging' => true
            ],
            'horizon' => [
                'command' => ['php', 'artisan', 'horizon'],
                'style' => ['cyan', null, ['bold']],
                'logging' => true,
                'restart' => [
                    'watch' => [
                        '.env',
                        'app/Jobs/*'
                    ]
                ]
            ],
            'reverb' => [
                'command' => ['php', 'artisan', 'reverb:start', '--verbose', '--debug'],
                'style' => ['magenta', null, ['bold']],
                'logging' => true,
            ],
        ];

        $processes = collect($processes)->mapWithKeys(function($input, $key) {
            $this->info('Starting ' . $key);
            $style = new OutputFormatterStyle(...$input['style']);
            $this->output->getFormatter()->setStyle($key, $style);
            $process = new Process(['php', 'artisan', 'run', $key, json_encode($input)]);
            $process->start();
            return [
                $key => [
                    'process' => $process,
                    'logging' => $input['logging'],
                ]
            ];
        });

        while (true) {
            if ($this->shouldExit) {
                break;
            }
            $processes->each(function($input, $key) {
                $process = $input['process'];
                if ($process->isRunning() && $input['logging']) {
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

        $processes->each(function($input, $key) {
            $process = $input['process'];
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

}
