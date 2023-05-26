<?php

use GoodSign\GoodSignAPI;
use GoodSign\Payload;

require __DIR__ . '/vendor/autoload.php';
require 'src/GoodSignAPI.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token, 'https://localhost:8000');

$payload = new Payload();
$payload->uuid = '83570f6b-4f28-49d2-b1ec-698f138f250c';
$payload->doc_name = 'NDA Simple.pdf';
$payload->metadata = ['any valid json works'];
$payload->addSigner('signer', 'John PHP', 'john@bluespark.co.nz', 0);

$response = $goodsign->useTemplate( $payload);
var_dump($response);
