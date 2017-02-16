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

Route::get('facebook/login', 'Facebook\FacebookController@Login');
Route::get('facebook/callback', 'Facebook\FacebookController@Callback');
Route::get('pages/index', 'Facebook\FacebookPageController@Index');
Route::match(['get','post'] , 'facebook/webhook','Facebook\FacebookController@Webhook');
Route::post('campaign/create' , 'Campaign\CampaignController@create');
Route::post('campaign/store', 'Campaign\CampaignController@store');
Route::get('campaign/{id}','Campaign\CampaignController@show')->name('campaign_live');
Route::get('facebook/test', 'Facebook\FacebookController@test');
Route::get('facebook/cron', 'Facebook\FacebookController@cron');
Route::get('/', 'Home\HomeController@index');
Route::get('facebook/keywords' , 'Facebook\FacebookController@assignKeywords');
