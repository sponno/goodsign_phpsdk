<?php
namespace GoodSign;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class GoodSignAPI
{
    private $api_token;
    private $client;

    // optional Base URL - use 'https://au.goodsign.io' for the Australian Datacenter
    public function __construct($api_token, $baseUrl = 'https://goodsign.io')
    {
        $this->api_token = $api_token;
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
        $response = $this->client->get("/api/document/{$uuid}");
        [$valid, $data] =  $this->proccessJson($response->getBody());

        return $data;

    }

    public function uploadPdf($filePath, $payload)
    {
        try {
            $response = $this->client->request('POST', '/api/uploadpdf', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->api_token,
                ],
                'multipart' => [[
                        'name'     => 'file',
                        'contents' => fopen($filePath, 'r')
                    ], [
                        'name'     => 'payload',
                        'contents' => json_encode($payload)
                    ]
                ]
            ]);

            $response = $this->client->get("/api/document/{$uuid}");
            [$valid, $data] =  $this->proccessJson($response->getBody());
            return $data;

        } catch (RequestException $e) {
            // Handle error
            echo $e->getMessage();
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

}