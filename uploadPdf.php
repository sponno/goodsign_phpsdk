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
$goodsign = new GoodSignAPI($api_token);


// Configure you requrest. If you're using a good IDE, you will find all configuration options
// start with either 'set' or 'add' autocomplete will do the rest.
$goodsign->setDocName( 'NDA Simple.pdf')
         ->setMetaData(['any valid json works'])
         ->addSigner('Signer1', 'John PHP', 'john@bluespark.co.nz', 0)

          // You can also place fields on to the document manually.
         // This could be difficult if you're not sure how many pages your document has.
         ->addExtraField(new ExtraField('signer1', 'sign', 1, 10, 10, 210, 20,1))
         ->addExtraField(new ExtraField('', 'label', "this is cool1", 10, 80, 210, 20,1))
         ->addExtraField(new ExtraField('', 'label', "Contents of your", 10, 80, 210, 10,1))

         // Find tags withing the document that are placeholders. This ar xref fields. They can become any type of input or label.
         //Labels auto-scale to height of box.
        ->addAttachment('./files/goodsign_guide_v1.4.pdf','Doc two from JOHN.pdf')
         ->addXref('xref1','[label|this is a label field]');

// Upload document and get response back.
$response = $goodsign->uploadPdf('./files/goodsign_guide_v1.4.pdf');
var_dump($response);
