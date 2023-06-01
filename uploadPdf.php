<?php

use GoodSign\ExtraField;
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

// You can also place fields on to the document manually.
// This could be difficult if you're not sure how many pages your document has.
$payload->addExtraField(new ExtraField('signer1', 'sign', 1, 10, 10, 210, 20,1));
$payload->addExtraField(new ExtraField('', 'label', "this is cool1", 10, 80, 210, 20,1));

//Labels auto-scale to height of box.
$payload->addExtraField(new ExtraField('', 'label', "Contents of your", 10, 80, 210, 10,1));

// Find tags withing the document that are placeholders. This ar xref fields. They can become any type of input or label.
$payload->addXref('xref1','[label|this is a label field]');
$payload->addXref('x_sign','[sign|signer1]');
$payload->addXref('x_someinput','[input|signer1|Your Name]');

// Upload document and get response back.
$response = $goodsign->uploadPdf('./files/goodsign_guide_v1.4.pdf', $payload);
var_dump($response);
