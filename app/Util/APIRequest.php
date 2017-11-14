<?php

namespace App\Util;

/**
 * Contains common function for making request to other web services.
 *
 * @package App\Util
 */
class APIRequest
{
    /**
     * The URL for the api request.
     *
     * @var string
     */
    private $url;

    /**
     * Contains the response of the api request.
     *
     * @var string
     */
    private $response;

    /**
     * Creates a new request to the given URL.
     *
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Performs a get request to the configured URL.
     */
    public function get()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url,
        ]);
        $this->response = curl_exec($curl);
        curl_close($curl);
        return $this;
    }

    /**
     * Returns the response as string.
     *
     * @return string
     */
    public function asString() {
        return $this->response;
    }

    /**
     * Decodes the response from json to an php object.
     *
     * @return array
     */
    public function asObject()
    {
        return json_decode($this->response);
    }
}