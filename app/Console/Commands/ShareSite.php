<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Sleep;
use Symfony\Component\Process\Process;

class ShareSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shares the site and stores the ngrok URL in the env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $process = new Process(['valet', 'share', '--log=stdout', '--log-format=json']);
        $process->start();
        while (true) {
            if ($process->isRunning()) {
                $output = $process->getIncrementalOutput();
                if (!empty($output)) {
                    $output = explode(PHP_EOL, $output);
                    collect($output)->filter()->each(function($output) {
                        $json = json_decode($output, true);
                        if (!empty($json['url'])) {
                            if (strpos($json['url'], 'https://') === 0) {
                                $this->comment('Sharing site at ' . $json['url']);
                                $this->setEnvironmentValue('NGROK_URL', $json['url']);
                            }
                        }
                    });
                }
            }
            Sleep::for(1)->seconds();
        }
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
