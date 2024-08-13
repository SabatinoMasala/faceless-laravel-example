<?php

namespace App\Jobs;

use App\Models\Story;
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
                'paragraph' => $group['text']
            ]);
        });

        $batch = collect($this->story->images)->map(function($image) {
            return new GenerateImage($this->story, $image);
        })->toArray();


        $this->appendToChain(function() use ($batch) {
            return Bus::batch($batch)
                ->then(function() {
                    \Log::info('done');
                })->dispatch();
        });
    }



    public function failed()
    {
        $this->story->update([
            'status' => 'IMAGES_FAILED',
        ]);
    }

}
