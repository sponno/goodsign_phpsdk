<?php
namespace GoodSign;

class Signer {
    public $contact;
    public $complete;
    public $reminderdays;
    public $completetime;
    public $verifysmspass;
    public $verifyid;
    public $template_rolename;
    public $key;
    public $emailStatus;
    public $emailopened;

    public function __construct($data) {
        $this->contact = $data['contact']??null;
        $this->complete = $data['complete'] ?? false;
        $this->reminderdays = $data['reminderdays'];
        $this->completetime = $data['completetime']??null;
        $this->verifysmspass = $data['verifysmspass']??null;
        $this->verifyid = $data['verifyid'];
        $this->template_rolename = $data['template_rolename'];
        $this->key = $data['key'];
        $this->emailStatus = $data['emailStatus']??null;
        $this->emailopened = $data['emailopened']??null;
    }
}