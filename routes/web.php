<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');

Route::get('mailchimp', 'MailchimpController@index');
Route::get('gettoken', 'MailchimpController@getToken');
Route::get('getmetadata', 'MailchimpController@getMetadata');
Route::get('getlists', 'MailchimpController@getLists');
Route::get('getmembersbylistid', 'MailchimpController@getMembersByListId');