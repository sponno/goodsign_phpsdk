<?php

namespace GoodSign;

class Payload
{
    public $uuid;
    public $doc_name;
    public $attachment_names_in_order = [];
    public $metadata = [];
    public $webhook = '';
    public $cc_email = '';      // seperate mulple email address with a comma, eg "a@b.com,c@d.com"
    public $smsverify = false;  // require contacts to verify their mobile phone number before signing
    public $send_in_order = false;
    public $duplicate = false;  // leave a false – only use if you're creating on-off templates with the API.
    public $email_subject = null;
    public $email_message = null;
    public $ignore_missing_signers = false; // set to true if you want GoodSign to delete extra signers found in your template.
    public $signers = [];
    public $xrefs = null;
    public $extrafields = null;

    public function addSigner($key, $name, $email, $reminder_days) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)===false) throw new \Exception("Invalid email address: $email");

        $this->signers[] = (Object)[
            'key' => $key,
            'name' => $name,
            'email' => $email,
            'reminder_days' => $reminder_days,
            'sort_order' => 0,
        ];
    }
    public function addExtraField(ExtraField $field) {
        $msg = $field->validateField();
        if($msg!="") throw new \Exception("Invalid extra field: $msg");
        $this->extrafields[] = (array)$field;
    }

    // Used when uploading a PDF that has "xref" tags. eg [x_somevalue] in the pdf. Programatically change them to other fields
    // See the goodsign_guildes_v1.4.pdf for more info on valid tags
    public function addXref(string $key /*eg x_somevalue */, string $value /*eg [sign|signer1] */ ){
        // check that the value is a valid text tag eg [ has | and ends with ]
        if(preg_match("/^\[.*\|.*\]$/", $value)==0) throw new \Exception("Invalid xref value: $value");
        $this->xrefs[$key] = $value;
    }



}