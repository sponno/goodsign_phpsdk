<?php
namespace GoodSign;

class Document {
    public $name;
    public $signers;
    public $fields;
    public $completed_time;
    public $status;
    public $cc;
    public $is_attachment;
    public $istemplate;
    public $webhook;
    public $metadata;
    public $note;
    public $uuid;
    public $download_pdf;
    public $download_zip;

    public function __construct($data) {
        $this->name = $data['name'];
        $this->completed_time = $data['completed_time']??null;
        $this->status = $data['status'];
        $this->cc = $data['cc'];
        $this->is_attachment = $data['is_attachment'];
        $this->istemplate = $data['istemplate'];
        $this->webhook = $data['webhook'];
        $this->metadata = $data['metadata']??null;
        $this->note = $data['note'];
        $this->uuid = $data['uuid'];
        $this->download_pdf = $data['download_pdf'];
        $this->download_zip = $data['download_zip'];

        $this->signers = array_map(function($item) {
            return new Signer($item);
        }, $data['signers'] ?? []);

        $this->fields = array_map(function($item) {
            return new Field($item);
        }, $data['fields'] ?? []);
    }
}
