<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require '../vendor/autoload.php';
require '../TelldusCredentials.php';
require '../TelldusApi.php';

use \Slim\Slim as Slim;

$app = new Slim();

$telldus = new TelldusApi(
    TelldusCredentials::PUBLIC_KEY,
    TelldusCredentials::PRIVATE_KEY,
    TelldusCredentials::TOKEN,
    TelldusCredentials::TOKEN_SECRET
);

function successJson($success=true)
{
    return json_encode(['success' => $success]);
}

function hideJs()
{
    return '<html><script type="text/javascript">window.onload = function() {window.location.href = "about:blank";};</script></html>';
}

$app->get('/device/:id/:action', function($id, $action) use ($telldus, $app) {
    $hide = $app->request->get('hide');

    switch ($action) {
        case 'on':
            $response = $telldus->on($id);
            if ($hide) {
                echo hideJs();
            } else {
                echo successJson($response);
            }
            break;
        case 'off':
            $response = $telldus->off($id);
            if ($hide) {
                echo hideJs();
            } else {
                echo successJson($response);
            }
            break;
        case 'toggle':
            $response = $telldus->toggle($id);

            if ($hide) {
                echo hideJs();
            } else {
                if (!$response) {
                    echo successJson(false);
                } else if (is_array($response)) {
                    echo json_encode(array_merge(['success' => true], $response));
                }
            }
            break;
    }
});

$app->get('/devices', function() use ($telldus) {
    $response = $telldus->listDevices();
    if (is_array($response)) {
        $devices = array_map(function($device) {
            return [
                'id' => $device['id'],
                'name' => $device['name']
            ];
        }, $response['device']);

        echo json_encode([
            'success' => true,
            'devices' => $devices
        ]);
    } else {
        echo successJson(false);
    }
});

$app->run();