<?php

namespace App\Http\Controllers;

use App\Jobs\BrainstormStoryTitle;
use App\Jobs\ChunkTranscript;
use App\Jobs\CreativeDirection;
use App\Jobs\GenerateImages;
use App\Jobs\GenerateStory;
use App\Jobs\GenerateVoiceOver;
use App\Jobs\RenderVideo;
use App\Jobs\TranscribeAudio;
use App\Models\Story;
use Illuminate\Support\Facades\Bus;
use Inertia\Inertia;

class StoriesController extends Controller
{
    public function store()
    {
        $story = Story::create([
            'status' => 'PENDING',
            'series' => request('subject'),
            'language' => 'English'
        ]);
        Bus::chain([
            new BrainstormStoryTitle($story),
            new GenerateStory($story),
            new GenerateVoiceOver($story),
            new TranscribeAudio($story),
            new ChunkTranscript($story),
            new CreativeDirection($story),
            new GenerateImages($story),
            new RenderVideo($story)
        ])->dispatch();
        return response()->redirectTo(route('stories.show', $story));
    }

    public function show(Story $story)
    {
        $story->load('images');
        return Inertia::render('Stories/Show', [
            'story' => $story
        ]);
    }

}
