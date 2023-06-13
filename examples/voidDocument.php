<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ .'/../src/GoodSignAPI.php';

use GoodSign\GoodSignAPI;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token);

// Call the getDocument method on the instance
//$document = $api->getDocument('a7ed6a69-f172-4d1b-b61d-aa4701f944a6');
$document = $goodsign->voidDocument('33a1edf0-d62d-464e-86be-ac16a7dce573');
var_dump($document);

