<?php

require '../vendor/autoload.php';
require '../TelldusCredentials.php';
require '../TelldusApi.php';

use \Slim\Slim as Slim;

$app = new Slim();

function createTelldus() {
    return new TelldusApi(
        TelldusCredentials::PUBLIC_KEY,
        TelldusCredentials::PRIVATE_KEY,
        TelldusCredentials::TOKEN,
        TelldusCredentials::TOKEN_SECRET
    );
}

$app->get('/device/:id/:action', function($id, $action) {
    $telldus = createTelldus();

    switch ($action) {
        case 'on':
            $telldus->on($id);
            break;
        case 'off':
            $telldus->off($id);
            break;
        case 'toggle':
            $telldus->toggle($id);
            break;
    }
});

$app->run();