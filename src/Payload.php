<?php

namespace GoodSign;

class Payload
{
    public $uuid;
    public $doc_name;
    public $attachment_names_in_order = [];
    public $metadata = [];
    public $webhook = '';
    public $cc_email = '';
    public $smsverify = false;
    public $send_in_order = false;
    public $duplicate = true;
    public $email_subject = null;
    public $email_message = null;
    public $ignore_missing_signers = false;
    public $signers = [];
    public $xrefs = null;

    public function addSigner($key, $name, $email, $reminder_days) {
        $this->signers[] = (Object)[
            'key' => $key,
            'name' => $name,
            'email' => $email,
            'reminder_days' => $reminder_days
        ];
    }

    // Missing is Fields and Adding Fields

    // Used when uploading a PDF that has "xref" tags. eg [x_somevalue] in the pdf. Programatically change them to other fields
    // See the goodsign_guildes_v1.4.pdf for more info on valid tags
    public function addXref(string $key /*eg x_somevalue */, string $value /*eg [sign|signer1] */ ){
        // check that the value is a valid text tag eg [ has | and ends with ]
        if(preg_match("/^\[.*\|.*\]$/", $value)==0) throw new \Exception("Invalid xref value: $value");
        $this->xrefs[$key] = $value;
    }

}