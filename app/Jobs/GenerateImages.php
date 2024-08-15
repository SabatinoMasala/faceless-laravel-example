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
         * Ideally, we're able to call $this->prependToChain(Bus::batch($jobs));
         * However, it seems the instance of Bus::batch($jobs) is of type PendingBatch instead of ChainedBatch
         * Bug ticket: https://github.com/laravel/framework/issues/52468
         * PR that changes this behaviour: https://github.com/laravel/framework/pull/52486
         *
         * For now, we'll have to create a new instance of ChainedBatch manually and prepend it to the chain
         */
        $chainedBatch = new ChainedBatch(Bus::batch($jobs));
        $this->prependToChain($chainedBatch);
    }

    public function failed()
    {
        $this->story->update([
            'status' => 'IMAGES_FAILED',
        ]);
    }

}
