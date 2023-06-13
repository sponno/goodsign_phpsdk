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
$document = $goodsign->getDocument('0404ef18-1b23-4de3-804a-d7e4eb40412f');
var_dump($document);

