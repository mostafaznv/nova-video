<?php

return [
    'ui' => [
        'player' => [
            /*
            |--------------------------------------------------------------------------
            | Player Type
            |--------------------------------------------------------------------------
            |
            | The default player type.
            | Available options: DEFAULT, VIDSTACK
            |
            */

            'type' => \Mostafaznv\NovaVideo\Enums\NovaVideoPlayerType::VIDSTACK,

            /*
            |--------------------------------------------------------------------------
            | Player Direction
            |--------------------------------------------------------------------------
            |
            | The default direction for player.
            | Available options: LTR, RTL
            |
            */

            'dir' => \Mostafaznv\NovaVideo\Enums\NovaVideoPlayerDirection::LTR
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Cover Uploader
    |--------------------------------------------------------------------------
    |
    | The default value for displaying cover uploader.
    |
    */

    'cover-uploader' => true
];
