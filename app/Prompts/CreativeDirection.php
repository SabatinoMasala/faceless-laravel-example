<?php

namespace App\Prompts;

use SabatinoMasala\LaravelLlmPrompt\BasePrompt;

class CreativeDirection extends BasePrompt
{

    public function __construct(
        public string $story,
        public string $series
    ){}

    public function getBasePrompt(): string
    {
        return 'You are a creative director. You are tasked with creating a visual style for characters and concepts in a story. From your styleguide, an animator will draw frames accompanying the story.
Respond in the following format:
name 1: A young man with a scar on his face, wearing a tattered cloak and a sword at his side.
location 1: A castle on a hill, surrounded by a moat and a drawbridge.
...

Do include precise descriptions of characters, and describe them in intricate detail.
Do include precise descriptions of locations, and describe them in intricate detail.
Do include precise descriptions of specific concepts, and describe them in intricate detail.
Do include precise descriptions of animals, beings, etc, and describe them in intricate detail.
Use keywords instead of sentences.

### context ###

The story is part of the series: {series}

This is the story: {story}';
    }
}
