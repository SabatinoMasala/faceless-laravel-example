<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use OpenAI;
use SabatinoMasala\Replicate\Replicate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        $this->app->bind(Replicate::class, function () {
            return new Replicate(env('REPLICATE_API_TOKEN'));
        });
        $this->app->bind('openai', function () {
            return OpenAI::client(env('OPENAI_API_KEY'));
        });
    }
}
