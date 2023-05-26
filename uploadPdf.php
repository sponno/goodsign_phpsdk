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
$payload->doc_name = 'NDA Simplexxxx.pdf';
$payload->metadata = ['any valid json works'];
$payload->addSigner('Signer1', 'John PHP', 'john@bluespark.co.nz', 0);

$response = $goodsign->uploadPdf('./files/goodsign_guide_v1.4.pdf', $payload);
var_dump($response);
