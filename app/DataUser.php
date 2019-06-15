<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataUser extends Model
{
    protected $fillable = [
        'user_id', 'mailchimp_token', 'mailchimp_dc',
    ];

    public $timestamps = false;
    protected $table = 'data_users';
}
