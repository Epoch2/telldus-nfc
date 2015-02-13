<?php

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

$app->get('/device/:id/:action', function($id, $action) use ($telldus) {
    switch ($action) {
        case 'on':
            echo successJson($telldus->on($id));
            break;
        case 'off':
            echo successJson($telldus->off($id));
            break;
        case 'toggle':
            echo successJson($telldus->toggle($id));
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
            'data' => $devices
        ]);
    } else {
        echo successJson(false);
    }
});

$app->run();