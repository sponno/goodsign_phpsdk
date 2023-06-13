<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ .'/../src/GoodSignAPI.php';

use GoodSign\GoodSignAPI;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$api_token = $_ENV['GOODSIGN_API_TOKEN'];

// Create an instance of the class
$goodsign = new GoodSignAPI($api_token);


$goodsign->setUUID('83570f6b-4f28-49d2-b1ec-698f138f250c')
    ->setDocName('NDA Simple2.pdf')
    ->setMetaData(['any valid json works'])
    ->addSigner('signer1', 'John PHP', 'john@bluespark.co.nz', 0);

$response = $goodsign->useTemplate();
var_dump($response);
