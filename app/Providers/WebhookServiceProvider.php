<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Objects\FacebookWebhook;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class WebhookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Route::bind('facebook_webhook' , function(){
            $facebookWebhook = new FacebookWebhook();
            $request = request();
            $verifyToken = 'Axb123xyz';
            if($request->isMethod('get')){
                if($verifyToken == $request->input('hub_verify_token')){
                    return $request->input('hub_challenge');
                }
            }
            else if($request->isMethod('post')){

                $facebookWebhook->field = $request->input('entry.0.changes.0.field');

                if($facebookWebhook->field == 'live_videos'){
                    $facebookWebhook->webhookLiveVideoId = $request->input('entry.0.changes.0.value.id');
                    $facebookWebhook->webhookLiveVideoStatus =$request->input('entry.0.changes.0.value.status');
                }
                else if($facebookWebhook->field == 'feed'){

                    $facebookWebhook->item = $request->input('entry.0.changes.0.value.item');

                    if($facebookWebhook->item == 'comment'){

                        $facebookWebhook->webhookCommentPostId = $request->input('entry.0.changes.0.value.post_id');
                        $facebookWebhook->webhookCommentId = $request->input('entry.0.changes.0.value.comment_id');
                        $facebookWebhook->webhookCommentSenderId = $request->input('entry.0.changes.0.value.sender_id');
                        $facebookWebhook->webhookCommentSenderName = $request->input('entry.0.changes.0.value.sender_name');
                        $facebookWebhook->webhookCommentBody = $request->input('entry.0.changes.0.value.message');

                    }
                }
            }
            return $facebookWebhook;
        });
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}