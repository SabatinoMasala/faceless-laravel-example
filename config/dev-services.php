<?php

return [
    'scripts' => [
        'share' => [
            'command' => ['php', 'artisan', 'share'],
            'style' => ['green', null, ['bold']],
            'logging' => true
        ],
        'horizon' => [
            'command' => ['php', 'artisan', 'horizon'],
            'style' => ['cyan', null, ['bold']],
            'logging' => true,
            'restart' => [
                'watch' => [
                    '.env',
                    'app/Jobs/*'
                ]
            ]
        ],
        'reverb' => [
            'command' => ['php', 'artisan', 'reverb:start', '--verbose', '--debug'],
            'style' => ['magenta', null, ['bold']],
            'logging' => true,
        ],
    ]
];
