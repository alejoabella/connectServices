<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Repositories\Mailchimp;

class MailchimpController extends Controller
{
    
    protected $mailchimp;

    public function __construct(Mailchimp $mailchimp)
    {
        $this->middleware('auth');
        $this->mailchimp = $mailchimp;
    }

    public function login()
    {   
        $baseUri = "https://login.mailchimp.com/oauth2/";
        return redirect($baseUri . 'authorize?response_type=code&client_id=' . env('MAILCHIMP_API_CLIENT_ID') . '&redirect_uri=' . env('MAILCHIMP_API_REDIRECT_URI'));
    }

    public function response(Request $request)
    {
        
        $this->mailchimp->getToken($request->input('code'));
        $this->mailchimp->getMetadata($apiKey);
    }

    public function metadata()
    {

        
        // Store metadata in DB
        dd($response->getBody()->getContents());

    }


    public function getLists()
    {
        $client = new Client([
            'base_uri' => 'https://us20.api.mailchimp.com/3.0/',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', 'lists', [
            'headers' => [
                'Authorization' => 'apikey ' . env('MAILCHIMP_API_KEY')
            ]
        ]);

        // Store List in DB
        dd($response->getBody()->getContents());
    }

    public function getMembersByListId()
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


/*         DB::table('users')->insert([
            ['email' => 'taylor@example.com'],
            ['email' => 'dayle@example.com']
        ]); */
        // Store all members in DB

    }

    public function processList()
    {

    }

}
