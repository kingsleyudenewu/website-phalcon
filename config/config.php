<?php

return new \Phalcon\Config(array(
    'database'    => array(
        'dbname'   => getenv('DB_HOST'),
    ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir'      => __DIR__ . '/../models/',
        'migrationsDir'  => __DIR__ . '/../migrations/',
        'viewsDir'       => __DIR__ . '/../views/',
        'baseUri'        => '/phalcon/',
        'cacheDir'       => '/cache/',
    )
));
