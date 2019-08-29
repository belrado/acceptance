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
            'host'      => 'localhost',
            'user'      => 'psuapi_db',
            'pass'      => 'api_db_sql!',
            'port'      => 3306,
            'dbname'    => 'psuapi_db'
        ],
        // jwt settings
        'jwt'       => [
            //'secret'    => 'supersecretkeyyoushouldnotcommittogithub'
            'secret'    => 'psumediumvapisupersecretkeywebbernardodev',
            'apikey'    => 'psueduorgmediprepumvietnam'
        ],
    ],
];
