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

$app->post(
    '/api/open',
    function(Request $request, Response $response, array $args) {
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
        $document_id = '';

        $querySelect = "SELECT * FROM files WHERE pathname = '" . $pathname . "';";
        $res = mysqli_query($db_conn, $querySelect);
        $row = mysqli_fetch_assoc($res);
        if ($row == NULL) {
            $document_id = generateRandomId(16);
            $queryInsert  = "INSERT INTO files (document_id, pathname) ";
            $queryInsert .= "VALUES ('" . $document_id . "', '" . $pathname . "');";
            mysqli_query($db_conn, $queryInsert);

            $res = mysqli_query($db_conn, $querySelect);
            $row = mysqli_fetch_assoc($res);
        }
        $document_id = $row['document_id'];

        $etherpad_config = (require(__DIR__ . '/../config.php'))['etherpad'];
        $file_content = file_get_contents($pathname);
        $instance = new EtherpadLite\Client(
            $etherpad_config['token'],
            $etherpad_config['host'] . '/api'
        );

        $already_exists = false;
        $pads = $instance->listAllPads();
        foreach ($pads->padIDs as $current_pad_id) {
            if ($current_pad_id == $document_id) {
                $already_exists = true;
                break;
            }
        }

        if (!$already_exists) {
            $instance->createPad($document_id, $file_content);
        }

        $users_count = ($instance->padUsersCount($document_id))->padUsersCount;
        if ($users_count == 0) {
            $instance->setText($document_id, $file_content);
        }

        return $response->withJson([
            'status' => 'success',
            'document_id' => $document_id,
            'url' => $etherpad_config['host'] . '/p/' . $document_id,
        ]);
});

$app->post(
    '/api/save/{document_id}',
    function(Request $request, Response $response, array $args) {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-type', 'application/json');

        $db_conn = require(__DIR__ . '/../database.php');

        $pathname = '';
        $document_id = $args['document_id'];

        $querySelect = "SELECT * FROM files WHERE document_id = '" . $document_id . "';";
        $res = mysqli_query($db_conn, $querySelect);
        $row = mysqli_fetch_assoc($res);
        if ($row == NULL) {
            return $response->withStatus(400)->withJson([
                'status' => 'error',
                'message' => 'Invalid pad id',
                'errors' => [
                    'document_id' => 'document_id is invalid',
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
        $instance = new EtherpadLite\Client(
            $etherpad_config['token'],
            $etherpad_config['host'] . '/api'
        );
        $file_content = ($instance->getText($document_id))->text;

        file_put_contents($pathname, $file_content);

        return $response->withJson([
            'document_id' => $args['document_id'],
            'status' => 'success',
            'message' => 'File saved',
        ]);
});
