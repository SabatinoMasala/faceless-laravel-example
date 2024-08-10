<?php

namespace App\Models;

class Story extends BaseModel
{

    protected $casts = [
        'voice_over_transcription' => 'json',
        'voice_over_chunks' => 'json'
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }

}
