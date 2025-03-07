<?php
    /*  Config file:
            For now it only holds database related info, and App version data, and is inteded to be loaded via the 'Loader' class.
     */
    return [
        /* Production specific settings */
        'production' => [
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'dbname' => 'collectie_tracker',
                'charset' => 'utf8mb4'
            ],
            'credentials' => [
                'username' => 'Verzameling_App',
                'password' => 'Z_VFQ(foXV*KxKPw'
            ],
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ],
            'version' => [
                'production' => 'V2.1 Alpha'
            ]
        ],
        /* Hotfix specific settings, not sure if usefull but included it never the less */
        'hotfix' => [
            'database' => [
                'host' => '#redacted#',
                'port' => 3306,
                'dbname' => 'collectie_tracker',
                'charset' => 'utf8mb4'
            ],
            'credentials' => [
                'username' => '#redacted#',
                'password' => '#redacted#'
            ],
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ],
            'version' => [
                'hotfix' => 'V2.1.1 Hotfixes'
            ]
        ],
        /* Live specific settings */
        'live' => [
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'dbname' => 'collectie_tracker',
                'charset' => 'utf8mb4'
            ],
            'credentials' => [
                'username' => 'Verzameling_App',
                'password' => 'Z_VFQ(foXV*KxKPw'
            ],
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT
            ],
            'version' => [
                'live' => 'V2.1.a2'
            ]
        ]
    ];
?>