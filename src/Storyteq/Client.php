<?php

namespace Storyteq;

use GuzzleHttp\RequestOptions;
use function GuzzleHttp\json_decode;
use GuzzleHttp\Client as GuzzleClient;


class Client
{
    protected $token;
    protected $version;
    protected $base;

    /**
     * @param [type] $version [description]
     * @param [type] $token   [description]
     */
    public function __construct($version, $token)
    {
       $this->token = $token;
       $this->version = $version;
       
       $this->init();
    }

    /**
     * @return [type] [description]
     */
    public function init()
    {
        $this->setBase();

        $this->client = new GuzzleClient([
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
                'Accept'        => 'application/json',
            ],
        ]);
    }

    /**
     * [setBase description]
     */
    public function setBase()
    {
        if ($this->version == 'v4'){
            $this->base = 'https://api.storyteq.com/v4/';
        } else {
            throw new \Exception("This version of the API is not supported");
        }
    }

    /**
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function readFeedData($id)
    {
        return $this->getRequest('content/feeds/'.$id);
    }

    /**
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function readMediaPublishements($id)
    {
        return $this->getRequest('content/media/'.$id.'/publishments/');
    }

    /**
     * @param  [type] $campaignId [description]
     * @return [type]             [description]
     */
    public function readDv360CampaignAds($campaignId)
    {
        return $this->getRequest('ads/external/dv360/campaigns/'.$campaignId.'/ads');
    }

    /**
     * @param  [type] $mediaId       [description]
     * @param  [type] $publishmentId [description]
     * @return [type]                [description]
     */
    public function readMediaPublishement($mediaId, $publishmentId)
    {
        return $this->getRequest('content/media/'.$mediaId.'/publishments/'.$publishmentId);
    }

    /**
     * @param  [type] $templateId [description]
     * @param  [type] $parameters [description]
     * @param  [type] $webhookUrl [description]
     * @param  [type] $batchId    [description]
     * @return [type]             [description]
     */
    public function createMedia($templateId, $parameters, $webhookUrl, $batchId = null)
    {
        $body = [
            'template_parameters' => $parameters,
            'notifications'       => [
                [
                    'type'  => 'webhook',
                    'route' => $webhookUrl,
                ],
            ],
        ];

        if ($batchId){
            $body['batch_id'] = $batchId;
        }

        return $this->postRequest('content/templates/'.$templateId.'/media', $body);
    }

    /**
     * @param  [type] $mediaId       [description]
     * @return [type]                [description]
     */
    public function readMedia($mediaId)
    {
        return $this->getRequest('content/media/'.$mediaId);
    }

    /**
    * @param  [type] $templateId [description]
    * @param  [type] $name       [description]
    * @return [type]             [description]
    */
    public function createBatch($templateId, $name)
    {
        $body = [
            'name' => $name
        ];

        return $this->postRequest('content/templates/'.$templateId.'/batches', $body);
    }

    /**
     * @param [type] $batchId
     * @param [type] $body
     * @return void
     */
    public function createBatchMedia($batchId, $body)
    {
        return $this->postRequest('content/batches/'.$batchId.'/media', $body);
    }

    /**
     * @param [type] $mediaId
     * @param [type] $type
     * @return void
     */
    public function publishMedia($mediaId, $type, $body = null)
    {
        return $this->postRequest('content/media/'.$mediaId.'/publish/'.$type, $body);
    }

    /**
     * @param [type] $endpoint
     * @param [type] $data
     * @return void
     */
    public function postRequest($endpoint, $data = null)
    {
        $response = json_decode(
            $this->client->post($this->base.$endpoint, [
                RequestOptions::JSON => $data,
            ])
            ->getBody()
            ->getContents()
        , true);

        return $response;
    }

    /**
     * @param [type] $endpoint
     * @param [type] $data
     * @return void
     */
    public function putRequest($endpoint, $data)
    {
        $response = json_decode(
            $this->client->put($this->base.$endpoint, [
                RequestOptions::JSON => $data,
            ])
            ->getBody()
            ->getContents()
        , true);

        return $response;
    }

    /**
     * @param [type] $endpoint
     * @param bool   $decode
     * @return void
     */
    public function getRequest($endpoint, $decode = true)
    {
        $response = $this->client->get($this->base.$endpoint)
            ->getBody()
            ->getContents();

        if ($decode) {
            return json_decode($response, true);
        }

        return $response;
    }
}