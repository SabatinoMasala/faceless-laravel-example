<?php

namespace App\Prompts;

class DescribeScene extends BasePrompt
{

    public function __construct(
        public string $story,
        public string $paragraph,
        public string $creativeDirection,
    ){}

    public function getBasePrompt(): string
    {
        return '### instruction ###
You are a story writing expert. You are tasked with creating a video script from a story, from which an animator will draw frames accompanying the story.
Respond only with a description of a scene for the animator to draw, nothing else.

Describe the characters, locations, and concepts in intricate detail to help the animator visualize the scene.
Be loyal to the creative direction and story provided.
If you refer to the characters, locations, and concepts in the story, make sure to visually describe them every time, for example:
Caesar (Muscular build, strong jawline, piercing brown eyes, brown hair, distinctive nose, laurel wreath on head, ornate armor with gold accents, crimson cape flowing behind him, commanding presence.), standing on top of a hill

Use keywords instead of sentences.
You will be penalized if you include any additional information like titles, headings, etc.
You will be penalized if you do not describe the characters, locations, and concepts in intricate detail.

### story ###

{story}

### Creative Direction ###

{creativeDirection}

### paragraph ###

Describe this paragraph: {paragraph}';
    }
}
