<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;

class Mailchimp {

    public function prepareUrl()
    {
        $baseUri = 'https://login.mailchimp.com/oauth2/authorize';
        $clientId = env('MAILCHIMP_API_CLIENT_ID');
        $redirectUri = env('MAILCHIMP_API_REDIRECT_URI');
        return $baseUri . '?response_type=code&client_id=' . $clientId . '&redirect_uri=' . $redirectUri;
    }

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

        return json_decode($response->getBody()->getContents())->access_token;
    }

    public function getMetadata($accessToken)
    {
        $client = new Client([
            'base_uri' => 'https://login.mailchimp.com/oauth2/metadata',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', 'metadata', [
            'headers' => [
                'User-Agent' => 'oauth2-draft-v10',
                'Accept'     => 'application/json',
                'Authorization'      => 'OAuth ' . $accessToken
            ]
        ]);

        return json_decode($response->getBody()->getContents())->get;
    }

    public function getLists($accessToken)
    {
        $client = new Client([
            'base_uri' => 'https://us20.api.mailchimp.com/3.0/',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', 'lists', [
            'headers' => [
                'Authorization' => 'apikey ' . $accessToken
            ]
        ]);

        return json_decode( $response->getBody()->getContents())->get;
    }

    public function getMembers()
    {
        $client = new Client([
            'base_uri' => 'https://us20.api.mailchimp.com/3.0/',
            'timeout'  => 0,
        ]);
        
        // Get all members
        $offset = 0;
        $count = 1000;

        do {

            $response = $client->request('GET', 'lists/828d1335f5/members?offset=' . $offset . '&count=' . $count, [
                'headers' => [
                    'Authorization' => 'apikey ' . env('MAILCHIMP_API_KEY')
                ]
            ]);

            $r = json_decode($response->getBody()->getContents())->members;

            $total = count($r);

            foreach ($r as $key) {
                echo $key->email_address . "<br>";
            }

            $offset += 1000;
            
        } while ($total >= 1000);
    }

    protected function storeMembers()
    {
/*         DB::table('users')->insert([
            ['email' => 'taylor@example.com'],
            ['email' => 'dayle@example.com']
        ]); */
        // Store all members in DB
    }

}