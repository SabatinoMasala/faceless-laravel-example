<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoriesController;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/api/stories/{story}', function(Story $story) {
    $story->load('images');
    return StoryResource::make($story);
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::group([
    'middleware' => ['auth', 'verified'],
], function() {
    Route::get('/stories', [StoriesController::class, 'index'])->name('stories.index');
    Route::post('/stories', [StoriesController::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}', [StoriesController::class, 'show'])->name('stories.show');
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
