<?php
namespace GoodSign;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;


class GoodSignAPI
{
    private $api_token;
    private $client;
    private $attachments = [];
    private $attachmentNames = [];
    private $baseUrl;

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
    }
    public function proccessJson($json){

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

    function useTemplate($data)
    {
        try {
            $response = $this->client->request('POST', '/api/usetemplate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->api_token,
                    'Content-Type' => 'application/json'
                ],
                'http_errors' => false,
                'json' => $data
            ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // Handle the exception as you need
            echo $e->getMessage();
        }
        [$valid, $data] =  $this->proccessJson($response->getBody());
        return $data;
    }

    // Adds an attachment to a document - can only be used with uploadPDF.
    public function addAttachment($filePath, $nameIncludingExtension )
    {
        $this->attachments[] = $filePath;
        $this->attachmentNames[] = $nameIncludingExtension; // eg nda.pdf
    }


    public function uploadPdf($filePath, $payload)
    {
        if(count($this->attachmentNames)>0){
            $payload->attachment_names_in_order = $this->attachmentNames; // add the names to the payload if we have attachments
        }
        $formData =  [
            [
                'name'     => 'file',
                'contents' => Utils::tryFopen($filePath, 'r')
            ], [
                'name'     => 'payload',
                'contents' => json_encode($payload)
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
            echo $e->getMessage();
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

}