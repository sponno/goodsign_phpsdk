<?php
namespace GoodSign;

class Field {
    public $complete;
    public $type;
    public $value;
    public $subtype;
    public $apiref;
    public $contact;

    public function __construct($data) {
        $this->complete = $data['complete'];
        $this->type = $data['type'];
        $this->value = $data['value'];
        $this->subtype = $data['subtype'];
        $this->apiref = $data['apiref'];
        $this->contact = $data['contact'];
    }
}