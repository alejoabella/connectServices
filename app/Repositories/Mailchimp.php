<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;
use App\Member;

class Mailchimp {

    protected $oauth2;
    protected $client;

    public function __construct()
    {
        $this->oauth2 = new Client([
            'base_uri' => 'https://login.mailchimp.com/oauth2/',
            'timeout'  => 2.0,
        ]);

        $this->client = new Client([
            'base_uri' => 'https://us20.api.mailchimp.com/3.0/',
            'timeout'  => 0,
        ]);
    }

    public function prepareUrl()
    {
        $baseUri = 'https://login.mailchimp.com/oauth2/authorize';
        $clientId = env('MAILCHIMP_API_CLIENT_ID');
        $redirectUri = env('MAILCHIMP_API_REDIRECT_URI');
        return $baseUri . '?response_type=code&client_id=' . $clientId . '&redirect_uri=' . $redirectUri;
    }

    public function getToken($code)
    {
        $response = $this->oauth2->request('POST', 'token',         [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('MAILCHIMP_API_CLIENT_ID'),
                'client_secret' => env('MAILCHIMP_API_CLIENT_SECRET'),
                'redirect_uri' => env('MAILCHIMP_API_REDIRECT_URI'),
                'code' => $code
            ]
        ]);

        return json_decode($response->getBody()->getContents())->access_token;
    }

    public function getMetadata($accessToken)
    {
        $response = $this->oauth2->request('GET', 'metadata', [
            'headers' => [
                'User-Agent' => 'oauth2-draft-v10',
                'Accept'     => 'application/json',
                'Authorization'      => 'OAuth ' . $accessToken
            ]
        ]);

        return json_decode($response->getBody()->getContents())->dc;
    }

    public function getLists($accessToken)
    {
        $response = $this->requestData($accessToken);
        return json_decode( $response->getBody()->getContents())->lists;
    }

    public function getMembers($accessToken, $listId)
    {
        // Get all members
        $offset = 0;
        $count = 1000;

        do {

            $response = $this->requestData($accessToken, $listId . '/members?offset=' . $offset . '&count=' . $count);
            $r = json_decode($response->getBody()->getContents())->members;
            $total = count($r);

            foreach ($r as $key) {
                Member::firstOrCreate(['email' => $key->email_address]);
            }

            $offset += 1000;
            
        } while ($total >= 1000);
    }

    private function requestData($accessToken, $url = '')
    {
        return $this->client->request('GET', 'lists/' . $url, [
            'headers' => [
                'Authorization' => 'apikey ' . $accessToken
            ]
        ]);
    }

}