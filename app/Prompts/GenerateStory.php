<?php

namespace App\Prompts;

use SabatinoMasala\LaravelLlmPrompt\BasePrompt;

class GenerateStory extends BasePrompt
{

    public function __construct(
        public string $language,
        public string $title,
        public int $maxCharacters = 1200,
    ){}

    public function getBasePrompt(): string
    {
        return 'You are tasked with writing a story based on the user prompt of a maximum of {maxCharacters} characters.
Only write a story, you will be penalized if you include titles, headings, etc.
Make sure the story has an ending.
The story should be written in {language}.
You will be penalized if the language is not {language}.
You will be penalized if the maximum amount of characters exceeds {maxCharacters}.

### Prompt ###
Write a story with the title: {title}
';
    }
}
