<?php

return [

    'user.passwordResetTokenExpire' => 3600,

    'cryptKey' => '123ewqasdcxz',

    'mime_types_to_display' => ['pdf', 'image'],

    'months' => [
        1  => 'January',
        2  => 'February',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ],

    'baseUrl' => env('BASE_URL', 'localhost'),

    // Will be merged by Setting::settings()

];
