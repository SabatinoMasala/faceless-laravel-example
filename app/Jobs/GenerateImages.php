<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Bus\ChainedBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class GenerateImages implements ShouldQueue
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
            'status' => 'IMAGES_START',
        ]);

        $this->story->images()->delete();
        collect($this->story->voice_over_chunks['groups'])->each(function($group) {
            $this->story->images()->create([
                'status' => 'PENDING',
                'paragraph' => $group['text'],
                'start' => $group['start'],
                'end' => $group['end'],
            ]);
        });

        $jobs = collect($this->story->images)->map(function($image) {
            return new GenerateImage($this->story, $image);
        });

        /**
         * Before Laravel 11.21.0, we had to do the following workaround:
         * $chainedBatch = new ChainedBatch(Bus::batch($jobs));
         * $this->prependToChain($chainedBatch);
         *
         * -> This was because Bus::batch($jobs) returned an instance of PendingBatch instead of ChainedBatch
         * Bug ticket regarding this: https://github.com/laravel/framework/issues/52468
         * PR that changes this behaviour: https://github.com/laravel/framework/pull/52486
         * This behaviour was changed in Laravel 11.21.0, allowing us to chain the batch directly
         */

        $this->prependToChain(Bus::batch($jobs));
    }

    public function failed()
    {
        $this->story->update([
            'status' => 'IMAGES_FAILED',
        ]);
    }

}
