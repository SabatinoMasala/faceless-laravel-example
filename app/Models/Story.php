<?php

namespace App\Models;

use App\Events\StoryStatusUpdated;

class Story extends BaseModel
{

    protected $casts = [
        'voice_over_transcription' => 'json',
        'voice_over_chunks' => 'json'
    ];

    static function boot()
    {
        parent::boot();
        static::updated(function($story) {
            if ($story->isDirty('status')) {
                StoryStatusUpdated::dispatch($story->id);
            }
        });
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

}
