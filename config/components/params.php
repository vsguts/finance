<?php

define('SECONDS_IN_DAY', 24 * 60 * 60);
define('SECONDS_IN_YEAR', SECONDS_IN_DAY * 365);

return [

    'user.passwordResetTokenExpire' => 3600,

    'cryptKey' => '123ewqasdcxz',

    'dirs' => [
        'file_safe' => '@app/files/',
        'file_stored' => '@app/files/stored/',
    ],

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

    // Will be merged by Setting::settings()

];
