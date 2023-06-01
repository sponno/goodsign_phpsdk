<?php

use GoodSign\GoodSignAPI;
require __DIR__ . '/vendor/autoload.php';
require 'src/GoodSignAPI.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token, 'https://localhost:8000');

// Call the getDocument method on the instance
//$document = $api->getDocument('a7ed6a69-f172-4d1b-b61d-aa4701f944a6');
$document = $goodsign->voidDocument('a6a2c021-a9d3-4ae8-98be-b159f03d3bdb');
var_dump($document);

//$templates = $goodsign->getTemplates();
//var_dump($templates);