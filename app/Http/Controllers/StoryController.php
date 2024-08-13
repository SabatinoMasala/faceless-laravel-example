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

class StoryController extends Controller
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
        return response()->redirectTo('/story/' . $story->id);
    }

    public function show(Story $story)
    {
        dispatch(new GenerateImages($story));
        $story->load('images');
        return Inertia::render('Story/Show', [
            'story' => $story
        ]);
    }

}
