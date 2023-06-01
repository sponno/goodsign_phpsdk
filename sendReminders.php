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
$document = $goodsign->sendReminder('7ae343b3-19e8-4642-beb0-8509ec65fa4e');
var_dump($document);
