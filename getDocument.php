<?php

require __DIR__ . '/vendor/autoload.php';
require 'src/GoodSignAPI.php';
use GoodSign\GoodSignAPI;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token, 'https://localhost:8000');

// Call the getDocument method on the instance
//$document = $api->getDocument('a7ed6a69-f172-4d1b-b61d-aa4701f944a6');
$document = $goodsign->getDocument('91f31155-b595-49e4-8f46-b043c9015240');
var_dump($document);

