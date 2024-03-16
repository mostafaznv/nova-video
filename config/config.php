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

            'dir' => \Mostafaznv\NovaVideo\Enums\NovaVideoPlayerDirection::LTR,


            /*
            |--------------------------------------------------------------------------
            | Max Height
            |--------------------------------------------------------------------------
            |
            | The default maximum height for the player on the index page.
            | Example: auto, 100px, etc.
            |
            */

            'max-height' => '160px',
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

    'cover-uploader' => true,

    /*
    |--------------------------------------------------------------------------
    | Mode
    |--------------------------------------------------------------------------
    |
    | The default mode for video field.
    | Available options: UPLOADED, URL
    |
    */

    'mode' => \Mostafaznv\NovaVideo\Enums\NovaVideoMode::UPLOADED,
];
