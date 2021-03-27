<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function generateRandomId($length = 16) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < $length; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

$app->post('/api/start', function(Request $request, Response $response, array $args) {
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-type', 'application/json');

    $db_conn = require(__DIR__ . '/../database.php');

    $params = json_decode($request->getBody(), true);
    if (!isset($params['file']) || $params['file'] == '') {
        return $response->withStatus(400)->withJson([
            'status' => 'error',
            'message' => 'Invalid parameters',
            'errors' => [
                'file' => 'file parameter is required',
            ],
        ]);
    }
    $pathname = $params['file'];

    $pad_id = '';

    $res = mysqli_query($db_conn, "SELECT * FROM files WHERE pathname = '" . $pathname . "';");
    $row = mysqli_fetch_assoc($res);
    if ($row == NULL) {
        $pad_id = generateRandomId(16);
        mysqli_query($db_conn, "INSERT INTO files (pad_id, pathname) VALUES ('" . $pad_id . "', '" . $pathname . "');");
        $res = mysqli_query($db_conn, "SELECT * FROM files WHERE pathname = '" . $pathname . "';");
        $row = mysqli_fetch_assoc($res);
    }
    $pad_id = $row['pad_id'];

    $etherpad_config = (require(__DIR__ . '/../config.php'))['etherpad'];
    $file_content = file_get_contents($pathname);
    $instance = new EtherpadLite\Client($etherpad_config['token'], $etherpad_config['host'] . '/api');

    $already_exists = false;
    $pads = $instance->listAllPads();
    foreach ($pads->padIDs as $current_pad_id) {
        if ($current_pad_id == $pad_id) {
            $already_exists = true;
            break;
        }
    }

    if (!$already_exists) {
        $instance->createPad($pad_id, $file_content);
    }

    $users_count = ($instance->padUsersCount($pad_id))->padUsersCount;
    if ($users_count == 0) {
        $instance->setText($pad_id, $file_content);
    }

    return $response->withJson([
        'status' => 'success',
        'pad_id' => $pad_id,
        'url' => $etherpad_config['host'] . '/p/' . $pad_id,
    ]);
});

$app->post('/api/save/{pad_id}', function(Request $request, Response $response, array $args) {
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-type', 'application/json');

    $db_conn = require(__DIR__ . '/../database.php');

    $pathname = '';
    $pad_id = $args['pad_id'];

    $res = mysqli_query($db_conn, "SELECT * FROM files WHERE pad_id = '" . $pad_id . "';");
    $row = mysqli_fetch_assoc($res);
    if ($row == NULL) {
        return $response->withStatus(400)->withJson([
            'status' => 'error',
            'message' => 'Invalid pad id',
            'errors' => [
                'pad_id' => 'pad_id is invalid',
            ],
        ]);
    }

    $pathname = $row['pathname'];
    if ($row['status'] == 1) {
        return $response->withStatus(403)->withJson([
            'status' => 'error',
            'message' => 'File is locked, you can\'t save it',
        ]);
    }

    $etherpad_config = (require(__DIR__ . '/../config.php'))['etherpad'];
    $instance = new EtherpadLite\Client($etherpad_config['token'], $etherpad_config['host'] . '/api');
    $file_content = ($instance->getText($pad_id))->text;

    file_put_contents($pathname, $file_content);

    return $response->withJson([
        'pad_id' => $args['pad_id'],
        'status' => 'success',
        'message' => 'File saved',
    ]);
});
