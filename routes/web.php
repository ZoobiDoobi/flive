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

Route::get('/', 'Facebook\FacebookController@Login');
Route::get('facebook/callback', 'Facebook\FacebookController@Callback');
Route::get('pages/index', 'Facebook\FacebookPageController@Index');
Route::match(['get','post'] , 'facebook/webhook','Facebook\FacebookController@Webhook');
Route::post('campaign/create' , 'Campaign\CampaignController@create');
Route::post('campaign/store', 'Campaign\CampaignController@store');

Route::get('facebook/test', 'Facebook\FacebookController@test');
Route::get('facebook/cron', 'Facebook\FacebookController@cron');
Route::get('/home', 'Home\HomeController@index');
Route::post('home/campaign', 'Home\HomeController@campaign');
Route::get('facebook/keywords' , 'Facebook\FacebookController@assignKeywords');
Route::post('home/imageupload' , 'Home\HomeController@imageUpload');


Route::get('campaign' , 'Campaign\CampaignController@index');
Route::post('campaign/saveFacebookPage' , 'Campaign\CampaignController@saveFacebookPage');
//Route::get('campaign/getLiveVideos' , 'Campaign\CampaignController@getLiveVideos');
Route::get('liveVideo/get' , 'LiveVideo\LiveVideosController@getLiveVideos');
Route::get('privacy' , 'Home\HomeController@privacy');
Route::get('campaign/get' , 'Campaign\CampaignController@get');
Route::get('campaigns/showAll' , 'Campaign\CampaignController@showAll');
Route::get('campaign/{id}' , 'Campaign\CampaignController@show');

Route::get('ajaxVotes/{campaignId}' , 'Campaign\CampaignController@ajaxVotes');
Route::get('facebook/unsubscribePages' , 'Facebook\FacebookController@cronPagesUnsubscribe');
