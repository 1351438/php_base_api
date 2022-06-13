<?php

const VALIDATOR_PATTERN = [
    "USUAL" => [
        "fingerprint" => [
            'type' => "STRING",
            'require' => true
        ]
    ],
    "LOGIN" => [
        "username" => [
            "type" => "INT",
            "require" => true
        ],
        "password" => [
            "type" => "STRING",
            "require" => true
        ]
    ],
    "SIGNUP" => [
        "username" => [
            "type" => "INT",
            "require" => true
        ],
        "password" => [
            "type" => "STRING",
            "require" => true
        ],
        "name" => [
            "type" => "STRING",
            "require" => true
        ]
    ]
];