<?php

use App\Http\Controllers\ProfileController;
use App\Models\Story;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/story/{story}', function(Story $story) {
    return $story;
});

Route::get('/brainstorm', function () {
    $story = \App\Models\Story::create([
        'status' => 'PENDING',
        'series' => 'Roman empire',
        'language' => 'English'
    ]);
//    $story = \App\Models\Story::find(1);
    Bus::chain([
        new \App\Jobs\BrainstormStoryTitle($story),
        new \App\Jobs\GenerateStory($story),
        new \App\Jobs\GenerateVoiceOver($story),
        new \App\Jobs\TranscribeAudio($story),
        new \App\Jobs\ChunkTranscript($story),
        // Creative direction
        // Image generation
    ])->dispatch();
    return $story;
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
