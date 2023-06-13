<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ .'/../src/GoodSignAPI.php';

use GoodSign\GoodSignAPI;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token);

$templates = $goodsign->getTemplates();
var_dump($templates);


