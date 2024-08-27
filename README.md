# Faceless video generator

Generate faceless videos on autopilot using Laravel.

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

## Remotion

In the `faceless` directory you will find a `remotion` project that is used to generate the videos.
You can run the following command to start the Remotion studio:

```
cd faceless
yarn install
yarn start
```

By default, the `faceless/src/example.json` file gets loaded with a dummy story that you can use to test the video generation.
If you want to generate a video with a different story, you can start the studio using the following parameter:
```
yarn start --props='{"json":"http:\/\/example.com\/story.json"}'
```
You can also control the FPS using the following parameter:

```
yarn start --props='{"json":"http:\/\/example.com\/story.json", "fps": 60}'
```

## Remotion Lambda

Remotion has a built-in Lambda renderer that can be used to render videos in the cloud.

Refer to the [Remotion documentation](https://www.remotion.dev/docs/lambda) for more information.

After you've set up a Lambda, you can add the following environment variable to your `.env` file:
```
REMOTION_APP_REGION=
REMOTION_APP_FUNCTION_NAME=
REMOTION_APP_SERVE_URL=
```

This way, [RenderVideo.php](https://github.com/SabatinoMasala/faceless-laravel-example/blob/fd82dd76df8d6157c1d13070e28450316a4f14e3/app/Jobs/RenderVideo.php#L42) will use the Lambda to render the video instead of the local Remotion instance.

## Packages used

- [sabatinomasala/laravel-llm-prompt](https://github.com/SabatinoMasala/laravel-llm-prompt/): This makes it easy to define LLM prompts.
- [sabatinomasala/replicate-php](https://github.com/SabatinoMasala/replicate-php): This is a PHP client for the Replicate API with 'run' functionality.
- [sabatinomasala/dev-scripts-for-laravel](https://github.com/SabatinoMasala/dev-scripts-for-laravel): This makes it easy to run several services in parallel during development.
