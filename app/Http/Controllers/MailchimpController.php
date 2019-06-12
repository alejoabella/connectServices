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
        return redirect($this->mailchimp->prepareUrl());
    }

    public function response(Request $request)
    {
        
        $accessToken = $this->mailchimp->getToken($request->input('code'));
        $this->mailchimp->getMetadata($accessToken);
    }

    public function showLists()
    {
        $lists = $this->mailchimp->getLists($accessToken);
    }

    public function getSelectedLists()
    {
        $members = $this->mailchimp->getMembers($accessToken);
    }

}
