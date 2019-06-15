<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Repositories\Mailchimp;
use App\DataUser;

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
        return redirect($this->mailchimp->prepareUrl());
    }

    public function response(Request $request)
    {
        
        $accessToken = $this->mailchimp->getToken($request->input('code'));
        $dc = $this->mailchimp->getMetadata($accessToken);

        DataUser::firstOrCreate(
            ['user_id' => Auth::id(), 'mailchimp_token' => $accessToken],
            ['mailchimp_dc' => $dc]
        );

        $lists = $this->mailchimp->getLists($accessToken);
/*         foreach ($lists as $key) {
            echo "<pre>";
            print_r($key);
        }  */
       
        return view('mailchimp-lists', ['lists' => $lists]);
    }

    public function getSelectedLists()
    {
        $members = $this->mailchimp->getMembers($accessToken);
    }

}
