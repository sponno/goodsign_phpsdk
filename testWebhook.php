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

// You can use this method to test your GoodSign WebHook Handler.
// This will simulate the webhook that GoodSign will send you application when something happens with your document.
// The webhook is 'dumb' – it will just tell you that something has happened with your document.
// When receiving your webhook, use the GetDocument API method to get the latest status of your document.

// Use https://webhook.site/ to get a URL to test your webhook if you would like to see the message
echo $goodsign->sendTestWebhook('1223','document_complete','');
