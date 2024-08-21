<?php

namespace App\Console\Commands;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Sleep;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class RunProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:process {key} {config}';

    protected $process;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->argument('key');
        $input = json_decode($this->argument('config'), true);
        $this->process = new Process($input['command']);
        $this->process->start();

        $watcher = null;
        if (!empty($input['restart']) && !empty($input['restart']['watch'])) {
            $watcher = $this->getWatchProcess($input['restart']['watch']);
        }

        while (true) {
            if ($watcher && $watcher->isRunning()) {
                $lines = explode(PHP_EOL, $watcher->getIncrementalOutput());
                $lines = array_filter($lines);
                collect($lines)->each(function($line) use ($key) {
                    if (!empty(trim($line))) {
                        $this->comment('Restarting ' . $key . ' due to event: ' . $line);
                        $this->process->stop();
                        // .env might've been updated
                        $this->process->setEnv(Dotenv::createArrayBacked(base_path())->load());
                        $this->process = $this->process->restart();
                    }
                });
            }
            if ($this->process->isRunning() && $input['logging']) {
                $output = $this->process->getIncrementalOutput();
                $errorOutput = $this->process->getIncrementalErrorOutput();
                if (!empty($output)) {
                    $output = explode(PHP_EOL, $output);
                    collect($output)->filter()->each(function($output) use ($key) {
                        $this->line(trim($output));
                    });
                }
                if (!empty($errorOutput)) {
                    $this->error(trim($errorOutput));
                }
            }
            Sleep::for(0.1)->seconds();
        }
    }

    protected function getWatchProcess($paths): Process
    {
        $command = [
            (new ExecutableFinder)->find('node'),
            realpath(__DIR__ . '/../bin/file-watcher.cjs'),
            json_encode($paths),
        ];

        \Log::info('Watching for changes on ' . json_encode($paths));

        $process = new Process(
            command: $command,
            timeout: null,
        );

        $process->start();

        return $process;
    }

}
