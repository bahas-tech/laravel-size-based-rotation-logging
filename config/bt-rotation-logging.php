<?php

return [
    'size_threshold_in_mb' => env('ROTATION_LOGGING_THRESHOLD_IN_MB', 500),
    'keep_logs_in_days' => env('ROTATION_LOGGING_KEEP_LOGS_IN_DAYS', 3),
    'channels' => [
        'size_based_rotation' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => Monolog\Handler\StreamHandler::class,
            'with' => [
                'stream' => storage_path('logs/laravel.log'),
            ],
            'tap' => [
                BahasTech\SizeBasedRotationLogging\Handler::class,
            ],
        ],
    ],
];
