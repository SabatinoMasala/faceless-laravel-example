<?php

use App\Http\Controllers\ProfileController;
use App\Prompts\Brainstorm;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/brainstorm', function () {
    $brainstorm = new Brainstorm('English', 'Roman empire');
    $brainstorm->addHistory([
        'The story about julius caesar'
    ]);
    $replicate = new SabatinoMasala\Replicate\Replicate(env('REPLICATE_API_TOKEN'));
    $output = $replicate->run('meta/meta-llama-3.1-405b-instruct', [
        'prompt' => $brainstorm->prompt(),
        'max_tokens' => 1000,
    ]);
    dd(collect($output)->join(''));
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
