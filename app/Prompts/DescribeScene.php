<?php

namespace App\Prompts;

class DescribeScene extends BasePrompt
{

    public function __construct(
        public string $story,
        public string $paragraph,
    ){}

    public function getBasePrompt(): string
    {
        return '### instruction ###
You are a story writing expert. You are tasked with creating a video script from a story, from which an animator will draw frames accompanying the story.
Respond only with a description of a scene for the animator to draw, nothing else.

Use keywords instead of sentences.

### story ###

{story}

### paragraph ###

Describe this paragraph: {paragraph}';
    }
}
