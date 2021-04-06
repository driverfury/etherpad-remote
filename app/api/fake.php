<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/api/fake/start', function(Request $request, Response $response, array $args) {
    $response = $response
        ->withHeader('Content-type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*');
    return $response->withJson([
        'status' => 'success',
        'document_id' => 'test_pad',
        'url' => 'http://127.0.0.1:9001/p/test_pad',
    ]);
});

$app->post('/api/fake/save/{document_id}', function(Request $request, Response $response, array $args) {
    $response = $response
        ->withHeader('Content-type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*');
    return $response->withJson([
        'document_id' => $args['document_id'],
        'status' => 'success',
        'message' => 'File saved',
    ]);
});
