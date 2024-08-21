<?php

namespace App\Prompts;

use SabatinoMasala\LaravelLlmPrompt\BasePrompt;

class Brainstorm extends BasePrompt
{

    public function __construct(
        public string $language,
        public string $series,
    ){}

    public function addHistory(array $history): void
    {
        $history = collect($history)->filter();
        if ($history->count() > 0) {
            $this->add('Make sure the following titles are not in the list:');
            $history->each(function($line) {
                $this->add('- ' . $line);
            });
        }
    }

    public function getBasePrompt(): string
    {
        return 'Give me a list of 30 story titles in {language} I can write.
Only respond with a list of titles, no other information.
1 title per line.
A good title consists of 4-8 words.
Do not number the list.
You will be penalized if the language is not {language}
The story should fit in the series: {series}';
    }
}
