<?php

namespace App\Repositories;

use GuzzleHttp\Client;

class Mailchimp {

    public function getToken($code)
    {
        $client = new Client([
            'base_uri' => 'https://login.mailchimp.com/oauth2/token',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('POST', 'token',         [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('MAILCHIMP_API_CLIENT_ID'),
                'client_secret' => env('MAILCHIMP_API_CLIENT_SECRET'),
                'redirect_uri' => env('MAILCHIMP_API_REDIRECT_URI'),
                'code' => $code
            ]
        ]);

        return $token = $response->getBody()->getContents();
    }

    public function getMetada($apiKey)
    {
        $client = new Client([
            'base_uri' => 'https://login.mailchimp.com/oauth2/metadata',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', 'metadata', [
            'headers' => [
                'User-Agent' => 'oauth2-draft-v10',
                'Accept'     => 'application/json',
                'Authorization'      => 'OAuth ' . $apiKey
            ]
        ]);
    }

}