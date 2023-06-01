<?php
namespace GoodSign;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;


class GoodSignAPI extends Payload
{
    private $api_token;
    private $client;
    private $attachments = [];
    private $attachmentNames = [];
    private $baseUrl;
    private Payload $payload;

    // optional Base URL - use 'https://au.goodsign.io' for the Australian Datacenter
    public function __construct($api_token, $baseUrl = 'https://goodsign.io')
    {
        $this->api_token = $api_token;
        $this->baseUrl = $baseUrl;
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_token,
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false
        ]);
        $this->payload = new Payload();
    }


    //start of fluent interface for payload
    public function setDocName($doc_name):GoodSignAPI{
        $this->payload->doc_name = $doc_name;
        return $this;
    }

    public function setUUID($uuid):GoodSignAPI{
        $this->payload->uuid = $uuid;
        return $this;
    }

    // Metadata is optional and can be anything. Stored on the document
    public function setMetaData(Object|array $metaData):GoodSignAPI{
        $this->payload->metadata = $metaData;
        return $this;
    }

    // adds a signer to the document - they will be notified about signing.
    public function addSigner($key, $name, $email, $reminder_days):GoodSignAPI{
        $this->payload->addSigner($key, $name, $email, $reminder_days);
        return $this;
    }

    // adds an extra field to the document
    public function addExtraField(ExtraField $extraField):GoodSignAPI{
        $this->payload->addExtraField($extraField);
        return $this;
    }

     // Adds an attachment to a document - can only be used with uploadPDF.
    public function addAttachment($filePath, $nameIncludingExtension )
    {
        $this->attachments[] = $filePath;
        $this->attachmentNames[] = $nameIncludingExtension; // eg nda.pdf
        return $this;
    }

    // Callback webhook - see testWebhook.php for an example
    public function setWebhook($url):GoodSignAPI
    {
        $this->payload->webhook = $url;
        return $this;
    }

    // comma seperated email list for people to cc on signing.
    // all signers get a copy of completed contract. All cc's get a copy of completed contract.
    public function setCCEmail($emails):GoodSignAPI{
        $this->payload->cc_email = $emails;
        return $this;
    }

    // require the signer to verify the mobile number via SMS before signing
    // this will cost a credit per signer
    public function setSmsVerfiy($bool):GoodSignAPI
    {
        $this->payload->smsverify = $bool;
        return $this;
    }

    // require the signers to sign in an order - one after the other
    // order is set by the order added to in the API.
    public function setSendInOrder($bool):GoodSignAPI{
        $this->payload->send_in_order = $bool;
        return $this;
    }

    // This is the subject of the email.
    public function setEmailSubject($subject):GoodSignAPI{
        $this->payload->email_subject = $subject;
        return $this;
    }

    // This is a small message included in the email below the signing link.
    // Do no use to give the signer instructions – use it if required to explain what this document is about
    public function setEmailMessage($message):GoodSignAPI
    {
        $this->payload->email_message = $message;
        return $this;
    }

    // some documents might have several signers. Set this to true, and GoodSign will remove
    // any signers from the document that you didn't send. Great for optional signers.
    public function setIgnoreMissingSigners($bool):GoodSignAPI{
        $this->payload->ignore_missing_signers = $bool;
        return $this;
    }





    public function getTemplates()
    {
        $response = $this->client->get('/api/templates');
        [$valid, $data] =  $this->proccessJson($response->getBody());
        if (!$valid) {
            return $data;
        }
        return array_map(function ($item) {
            return new Document($item);
        }, $data);
    }



    public function getDocument($uuid )
    {
        $response = $this->client->get("/api/document/{$uuid}");
        [$valid, $data] =  $this->proccessJson($response->getBody());
        if (!$valid) {
            return $data;
        }
        return new MasterDocument($data);
    }

    function useTemplate()
    {
        try {
            $response = $this->client->request('POST', '/api/usetemplate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->api_token,
                    'Content-Type' => 'application/json'
                ],
                'http_errors' => false,
                'json' => $this->payload
            ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // Handle the exception as you need
            echo $e->getMessage();
        }
        [$valid, $data] =  $this->proccessJson($response->getBody());
        return $data;
    }


    // upload a PDF to GoodSign and start the signing process. This PDF will have texttags in the document
    // eg [sign|signer1] which will be processed by GoodSign - see the GoodSign_Guide in the files folder for examples of all possible tags
    public function uploadPdf($filePath)
    {
        if(count($this->attachmentNames)>0){
            $this->payload->attachment_names_in_order = $this->attachmentNames; // add the names to the payload if we have attachments
        }
        $formData =  [
            [
                'name'     => 'file',
                'contents' => Utils::tryFopen($filePath, 'r')
            ], [
                'name'     => 'payload',
                'contents' => json_encode($this->payload)
            ]
        ];

        // add any file attachments
        if(count($this->attachments) > 0){
            foreach ($this->attachments as $attachment) {
                $item['name'] = 'attachments[]';
                $item['contents'] = Utils::tryFopen($attachment,'r');
                $formData[] = $item;
            }
        }

        try {
            $response = $this->client->request('POST', '/api/uploadpdf', [
                'multipart' => $formData
            ]);
            [$valid, $data] =  $this->proccessJson($response->getBody());
            return $data;

        } catch (RequestException $e) {
            // Handle error
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function voidDocument($uuid)
    {
        $response = $this->client->post('/api/document/' . $uuid . '/void');
        [$valid, $data] =  $this->proccessJson($response->getBody());
        return $data;
    }

    public function sendReminder($uuid)
    {
        $response = $this->client->post('/api/document/' . $uuid . '/remindall');
        [$valid, $data] =  $this->proccessJson($response->getBody());
        return $data;
    }

    public function sendTestWebhook(string $uuid,string $type /* must be valid type, see list below*/, string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("Invalid URL");
        }
        // Valid webhook types
        $validTypes = ['document_created', 'signer_opened', 'signer_complete', 'signer_rejected', 'signer_bounced', 'document_complete', 'document_voided'];
        if(!in_array($type, $validTypes)){
            throw new \Exception("Invalid webhook type, must be one of the following 'document_created', 'signer_opened', 'signer_complete', 'signer_rejected', 'signer_bounced', 'document_complete', 'document_voided'");
        }
        $payload = [
            'uuid' => $uuid,
            'status' => $type,
        ];

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $url,
            // You can set any number of default request options.
            'timeout' => 2.0,
            'verify' => false, // This is equivalent to "CURLOPT_SSL_VERIFYHOST" and "CURLOPT_SSL_VERIFYPEER" set to false.
        ]);

        $response = $client->post($url, [
            RequestOptions::JSON => $payload,
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = $response->getBody();
        return $body->getContents();
    }

    // fixes an issue when invalid json comes back or slightly different messaging formatting.
    private function proccessJson($json){
       $data = json_decode($json, true);
        // catch invalid json
       if(json_last_error()!=JSON_ERROR_NONE){
           return [false, ['success'=>false, 'msg'=>'Invalid JSON', 'server_data'=>$json]];
       }
        if (isset($data['message'])) {
            $response['success'] = false;
            $response['msg'] = $data['message'];
            return [false, $response];
        }

        return [true, $data];
    }


}