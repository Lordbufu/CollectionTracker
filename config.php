<?php
    /* For obvious reasons this is only used in a local testing enviroment, plz edit for production. */
    return [
        'database' => [
            'name' => 'collectie_tracker',
            'username' => 'Verzameling_App',
            'password' => 'Z_VFQ(foXV*KxKPw',
            'connection' => 'mysql:host=127.0.0.1',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        ]
    ];
?>