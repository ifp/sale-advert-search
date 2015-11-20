<?php

namespace IFP\Adverts\Sales;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use IFP\Adverts\AdvertNotFoundException;
use IFP\Adverts\InvalidApiTokenException;
use IFP\Adverts\InvalidSearchCriteriaException;

class SearchClient
{
    use QueryStringTrait;

    private $client;

    public function __construct($base_url, $token)
    {
        $this->client = new Client([
            'base_uri' => $base_url,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    }

    public function find($id)
    {
        try {
            $response = $this->client->get('adverts/sales/' . $id);

            return json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new InvalidApiTokenException;
                    break;
                case 404:
                    throw new AdvertNotFoundException($e);
                    break;
                default:
                    throw $e;
            }
        }
    }

    public function search($params)
    {
        try {
            $query_string = $this->buildQueryString($params);

            $response = $this->client->get('adverts/sales/search?' . $query_string);

            return json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 400:
                    throw new InvalidSearchCriteriaException($e);
                    break;
                case 401:
                    throw new InvalidApiTokenException;
                    break;
                default:
                    throw $e;
            }
        }
    }

}

