<?php


return [
    'auth' => [
        'throttle' => [
            'validate' => "10"
        ],
        'security' => [
            "Secret-Token" => "BLABLA",
            "User-Agent" => "BLABLA"
        ]
    ],
    'private' => [
        'throttle' => [
            'validate' => "10"
        ],
        'security' => [
            "User-Agent" => "BLABLA",
            "Allowed-IP" => "12123"
        ]
    ]
];
