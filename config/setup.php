<?php

return [
    /*
    |--------------------------------------------------------------------------
    | One-time installation token
    |--------------------------------------------------------------------------
    |
    | Set a random value of at least 32 characters on a new hosting provider.
    | The setup endpoint becomes unavailable as soon as an admin exists.
    |
    */
    'token' => env('SETUP_TOKEN'),
];
