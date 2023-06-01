<?php

use GoodSign\GoodSignAPI;
require __DIR__ . '/vendor/autoload.php';
require 'src/GoodSignAPI.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token);

// Call the getDocument method on the instance
$document = $goodsign->sendReminder('0e479a0e-d183-4b83-8388-646b2ac86842');
var_dump($document);
