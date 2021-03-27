<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$config = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$app = new Slim\App($config);

$app->get('/install', function($request, $response, $args) {
    $install = require_once('../app/install.php');
    $res = $install();
    if ($res === true) {
        return $response->withStatus(200)->write('[OK] INSTALLATION');
    } else {
        return $response->withStatus(500)->write('[ERROR] MySQL: ' .  $res);
    }
});

require_once('../app/api/v1.php');

$app->run();
