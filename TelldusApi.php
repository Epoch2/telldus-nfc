<?php

//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

require_once 'HTTP/OAuth/Consumer.php';

class TelldusApi
{
    const RESPONSE_TYPE_JSON = 'json';
    const RESPONSE_TYPE_XML = 'xml';

    const BASE_URL = 'http://api.telldus.com';
    const REQUEST_TYPE = 'GET';

    const STATE_ON = 1;
    const STATE_OFF = 2;

    private $publicKey;
    private $privateKey;
    private $token;
    private $tokenSecret;

    private $consumer;

    public function __construct($publicKey, $privateKey, $token, $tokenSecret)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;

        $this->consumer = new HTTP_OAuth_Consumer(
            $this->publicKey,
            $this->privateKey,
            $this->token,
            $this->tokenSecret
        );
    }

    private function request(array $action, array $parameters=[])
    {
        $actionString = implode('/', $action);
        $requestUrl = self::BASE_URL . '/' . self::RESPONSE_TYPE_JSON . '/' . $actionString;

        $response = $this->consumer->sendRequest($requestUrl, $parameters, self::REQUEST_TYPE);

        return json_decode($response->getBody(), true);
    }

    public function on($id)
    {
        if (is_numeric($id)) {
            $id = (int)$id;

            $this->request(['device', 'turnOn'], ['id' => $id]);
        }
    }

    public function off($id)
    {
        if (is_numeric($id)) {
            $id = (int)$id;

            $this->request(['device', 'turnOff'], ['id' => $id]);
        }
    }

    public function toggle($id)
    {
        if (is_numeric($id)) {
            $id = (int)$id;

            $response = $this->request(['device', 'history'], ['id' => $id, 'to' => time()]);

            // Walk backwards in history until we find the last successful command
            // which will indicate the device's current status

            $history = array_reverse($response['history']);
            $lastSuccessfulCommand = null;

            foreach ($history as $command) {
                if ($command['successStatus'] == 0) {
                    $lastSuccessfulCommand = $command['state'];
                    break;
                }
            }

            switch ($lastSuccessfulCommand) {
                case self::STATE_OFF:
                    $this->on($id);
                    break;
                case self::STATE_ON:
                    $this->off($id);
                    break;
                default:
                    // On seems like a sane default...
                    $this->on($id);
                    break;
            }
        }
    }

}