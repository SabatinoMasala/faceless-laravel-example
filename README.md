# Faceless video generator

## Getting started

Run the following commands to get started:

```
composer install
yarn install
cp .env.example .env
php artisan key:generate
```

Fill out the following `.env` variables:
```
REPLICATE_API_TOKEN= # https://replicate.com/account/api-tokens
OPENAI_API_KEY= # https://platform.openai.com/api-keys
```

Run the following command to start the dev services:
```
php artisan dev
```

This will start horizon, reverb and share the app using `valet share`.
The app should be available over the internet for Replicate to access it.

## Packages used

- sabatinomasala/laravel-llm-prompt: This makes it easy to define LLM prompts.
- sabatinomasala/replicate-php: This is a PHP client for the Replicate API with 'run' functionality.
- sabatinomasala/dev-scripts-for-laravel: This makes it easy to run several services in parallel during development.
