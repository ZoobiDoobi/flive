<?php

namespace App\Objects;

class FacebookWebhook{


    public $hubChallenge; //we need this when facebook sends get request to verify the callback


    public $field; //we have two kinds of fields, live_videos, feed

    public $item; //it can be comment, like, share, or anything else

    public $webhookLiveVideoId;

    public $webhookLiveVideoStatus;

    public $webhookCommentPostId;

    public $webhookCommentId;

    public $webhookCommentSenderId;

    public $webhookCommentSenderName;

    public $webhookCommentBody;


    public function getField(){
        return $this->field;
    }

    public function getItem(){
        return $this->item;
    }

    public function getPostId(){
        return $this->webhookCommentPostId;
    }
}