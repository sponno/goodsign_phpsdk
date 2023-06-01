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
         ->addSigner('Signer1', 'John PHP', 'john@bluespark.co.nz', 1)  // remind every day - reminds are triggered 5x before giving up.

          // You can also place fields manually into the docment if needed.
         // This could be difficult if you're not sure how many pages your document has.
         // Current limitation – you can only add extra fields to the main document - not attachments
         ->addExtraField((new ExtraField())
                ->setKey('signer1')
                ->setType('sign')
                ->setTag('[label|this is a label field]')  // tags as defined in GoodSign Guide
                ->setPage(1)
                ->setTop(10)
                ->setLeft(10)
                ->setWidth(210)
                ->setHeight(20)
          )
         ->addExtraField((new ExtraField())
                ->setKey('signer1')
                ->setType('input')
                ->setTag('[input|Default Text]')
                ->setPage(1)
                ->setTop(40)
                ->setLeft(10)
                ->setWidth(210)
                ->setHeight(10)
         )
         ->addExtraField((new ExtraField())
                ->setType('label')
                ->setTag('[label|Label will be rendered on the document]')
                ->setPage(1)
                ->setTop(80)
                ->setLeft(10)
                ->setWidth(210)
                ->setHeight(10)
         )

         // Find tags withing the document that are placeholders. This ar xref fields. They can become any type of input or label.
         // Labels auto-scale to height of box.
        ->addAttachment('./files/goodsign_guide_v1.4.pdf','Doc two from JOHN.pdf')
        ->addXref('xref1','[label|this is a label field]');


// Upload document and get response back.
$response = $goodsign->uploadPdf('./files/goodsign_guide_v1.4.pdf');
var_dump($response);
