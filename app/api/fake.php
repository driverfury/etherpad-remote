<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/api/fake/start', function(Request $request, Response $response, array $args) {
    $response = $response
        ->withHeader('Content-type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*');
    return $response->withJson([
        'status' => 'success',
        'pad_id' => 'test_pad',
        'url' => 'http://127.0.0.1:9001/p/test_pad',
    ]);
});

$app->post('/api/fake/save/{pad_id}', function(Request $request, Response $response, array $args) {
    $response = $response
        ->withHeader('Content-type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*');
    return $response->withJson([
        'pad_id' => $args['pad_id'],
        'status' => 'success',
        'message' => 'File saved',
    ]);
});
