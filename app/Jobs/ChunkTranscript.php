<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ChunkTranscript implements ShouldQueue
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
        $this->story->update([
            'status' => 'CHUNKING_START',
        ]);
        $sentences = [];
        $currentSentence = '';
        $startTime = 0;
        $endTime = 0;

        $chunks = $this->story->voice_over_transcription['chunks'];

        foreach ($chunks as $index => $chunk) {
            if ($index === 0) {
                // Set the start time at the beginning of the first chunk
                $startTime = $chunk['timestamp'][0];
            }

            // Append the chunk's text to the current sentence
            $currentSentence .= $chunk['text'];
            // Update the end time with the end of the current chunk
            $endTime = $chunk['timestamp'][1];

            // Check if the chunk text ends with a sentence-ending punctuation
            if (preg_match('/[.?!]\s*$/', $chunk['text'])) {
                // Trim and add the current sentence with its timing to the sentences array
                $sentences[] = [
                    'text' => trim($currentSentence),
                    'start' => $startTime,
                    'end' => $endTime
                ];
                // Reset the current sentence to start building the next one
                $currentSentence = "";
                // Set the start time of the next sentence to the start of the next chunk
                if (isset($chunks[$index + 1])) {
                    $startTime = $chunks[$index + 1]['timestamp'][0];
                }
            }
        }

        // Handle any remaining text that didn't end with a punctuation mark
        if (trim($currentSentence) !== "") {
            $sentences[] = [
                'text' => trim($currentSentence),
                'start' => $startTime,
                'end' => $endTime
            ];
        }

        $groups = collect($sentences)->chunk(2)->map(function($sentences) {
            return [
                'text' => $sentences->pluck('text')->join(' '),
                'start' => $sentences->first()['start'],
                'end' => $sentences->last()['end'],
            ];
        });

        $this->story->update([
            'voice_over_chunks' => [
                'groups' => $groups->values(),
                'sentences' => $sentences,
            ],
            'status' => 'CHUNKING_END',
        ]);
    }
}
