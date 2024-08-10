<?php

namespace App\Prompts;

use ReflectionObject;
use ReflectionProperty;

abstract class BasePrompt
{
    abstract public function getBasePrompt(): string;
    private $additional = [];

    public function getAdditionalPrompt(): string {
        return collect($this->additional)->join(PHP_EOL);
    }

    protected function addAdditionalPromptLine(string $line): void
    {
        $this->additional[] = $line;
    }

    public function replaceVariables(string $string): string
    {
        $reflectionClass = new ReflectionObject($this);
        $vars = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        collect($vars)->each(function ($property, $key) use ($vars, &$string, $reflectionClass) {
            $value = $this->{$property->getName()};
            $string = str_replace('{' . $property->getName() . '}', $value, $string);
        });
        return $string;
    }

    public function get(): string
    {
        $prompt = $this->getBasePrompt() . PHP_EOL . $this->getAdditionalPrompt();
        return $this->replaceVariables($prompt);
    }

    public function __toString()
    {
        return $this->get();
    }

}
