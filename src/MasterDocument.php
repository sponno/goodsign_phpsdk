<?php
namespace GoodSign;

class MasterDocument extends Document {
    public $attachments;

    public function __construct($data) {
         parent::__construct($data['master_doc']);

          $this->attachments = array_map(function($item) {
            return new Document($item);
        }, $data['attachments'] ?? []);
    }
}