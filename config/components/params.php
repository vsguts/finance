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
        1  => __('January'),
        2  => __('February'),
        3  => __('March'),
        4  => __('April'),
        5  => __('May'),
        6  => __('June'),
        7  => __('July'),
        8  => __('August'),
        9  => __('September'),
        10 => __('October'),
        11 => __('November'),
        12 => __('December'),
    ],

    // Will be merged by Setting::settings()

];
