<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = new Slim\App();

//$app->get('/asd', function($request, $response, $args) {
//    $response->write('Welcome to Slim');
//});
//
$app->get('/', function($request, $response, $args) {
    $countries = [
        ['name' => 'USA'],
        ['name' => 'India'],
        ['name' => 'Argentina'],
        ['name' => 'Germany'],
    ];
    return $response->withJson($countries);
});

$app->get('/install', function($request, $response, $args) {
    require_once('../app/install.php');
    return $response->write('[OK] INSTALLATION');
});

require_once('../app/api/fake.php');

$app->run();
