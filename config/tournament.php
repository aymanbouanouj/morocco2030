<?php

return [
    'timezone' => env('TOURNAMENT_TIMEZONE', 'Africa/Casablanca'),

    'countdown' => [
        'title' => env('TOURNAMENT_COUNTDOWN_TITLE', 'Tournament Countdown'),
        'label' => env('TOURNAMENT_COUNTDOWN_LABEL', 'Opening Match'),
        'target' => env('TOURNAMENT_COUNTDOWN_TARGET', '2030-06-08 20:00:00'),
    ],
];
