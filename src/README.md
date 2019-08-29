# Default settings -
create src/settings.php

<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Databases settings
        'db'        => [
            'host'      => 'DBHOST',
            'user'      => 'USERNAME',
            'pass'      => 'DBPASSWORD',
            'port'      => PORT,
            'dbname'    => 'DBNAME'
        ],

        // jwt settings
        'jwt'       => [
            // your secret code
            'secret'    => 'supersecretkeyyoushouldnotcommittogithub'
            'apikey'    => 'psueduorgmediprepumvietnam'
        ],
    ],
];
