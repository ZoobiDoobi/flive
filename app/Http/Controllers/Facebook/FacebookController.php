<?php

namespace App\Http\Controllers\Facebook;

use App\Objects\FacebookWebhook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\User;
use App\Models\Comment;
use App\Models\Keyword;
use Illuminate\Support\Facades\Storage;
use App\Models\LiveVideo;
use DB;


class FacebookController extends Controller
{
    private $fb;

    function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        $this->fb = $fb;
    }
    

    public function Login($value='')
    {
    	# code...
        $loginUrl = $this->fb->getLoginUrl();
        return view('facebook.connect',['loginUrl' => $loginUrl]);
    	
    }

    public function Callback($value='')
    {
    	# code...
        try {
            $token = $this->fb->getAccessTokenFromRedirect();
            
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        //Access token will be null if user denied the request
        if(!$token){
            //Get the redirect helper
            $helper = $this->fb->getRedirectLoginHelper();

            if(! $helper->getError()){
                abort(403,'Unauthorized Action.');
            }

            //User denied the request
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        if(! $token->isLongLived()){
            //OAuth Client 
            $oauthClient = $this->fb->getOAuth2Client();

            //Extend the access token
            try {
                $token = $oauthClient->getLongLivedAccessToken($token);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        $this->fb->setDefaultAccessToken($token);
        //Getting user specific information and store it in databse using Eloquent User Model
        try {
            $response = $this->fb->get('/me?fields=id,name,email');

        } catch (Exception $e) {
            dd($e->getMessage());
        }

        $userNode = $response->getGraphUser();
        //Put in the session for later use
        Session::put('facbook_access_token', $token);
        Session::put('fb_user_id', $userNode->getId()); // put the user id in session, we would retrieve

        //Save the user to database, if it already exists,otherwise update it
         $user = User::where('facebook_user_id', $userNode->getId())->first();

         if( ! $user)
         {  //User does not exist
            $user = new User;
            $user->name = $userNode->getName();
            $user->email = $userNode->getEmail();
            $user->facebook_user_id = $userNode->getId();
            $user->access_token = $token;
            $user->active = 1;
            if($user->save()){
                Session::put('local_user_id', $user->id);
                return redirect('campaign');
            }
            else{
                dd($user);
            }
         }
         else
         {  //user exist we need to update its info
            $user->name = $userNode->getName();
            $user->email = $userNode->getEmail();
            $user->access_token = $token;
            $user->active = 1;
            if($user->save()){
                Session::put('local_user_id', $user->id);
                return redirect('campaign');
            }
            else{
                dd($user);
            }
         }
        
    }

    public function test($value='')
    {
        # code...

        
    }

    public function assignKeywords($comment)
    {
        # code...

        $keywords = Keyword::where('live_video_id' , $comment['live_video_id'])->get()->toArray();
        $commentBody = strtolower($comment['comment_body']);

        foreach($keywords as $keyword){
            $keywordBody = strtolower($keyword['keyword_name']);
            if(strstr($commentBody , $keywordBody) !== FALSE){
                $comment['keyword_id'] = $keyword['id'];
            }
        }
        return $comment;
    }

    public function cron($value='')
    {
        # code...
        $liveVideos = LiveVideo::where('status' , 'live')->get();
        
        foreach($liveVideos as $liveVideo) 
        {
            # code...
            if($liveVideo->status == 'live')
            {
                $user = User::where('facebook_user_id' , $liveVideo->fb_user_id)->first();
                $this->fb->setDefaultAccessToken($user->access_token);
                try {
                    $response = $this->fb->get('/' . $liveVideo->live_vidoe_id . '/comments');
                    $comments = $response->getGraphEdge()->asArray();

                    //////////////////////////////Saving Comment/////////////////////////////
                    $data = array();
                    $count = 0;
                    foreach ($comments as $comment) 
                    {    
                        //check if comment already exists in our db
                        $commentExist = $this->commentExists($comment['id']);

                        $authorExist = $this->commentAuthorExists($comment['from']['id'] , $liveVideo->live_vidoe_id);
                        if( !($commentExist)){
                            if(!($authorExist)){
                                $data[$count] = array(
                                    'comment_id' => $comment['id'],
                                    'comment_body' => $comment['message'],
                                    'comment_author_id' => $comment['from']['id'],
                                    'comment_author_name' => $comment['from']['name'],
                                    'active' => 1,
                                    'keyword_id' => null,
                                    'live_video_id' => $liveVideo->live_vidoe_id
                                );
                                $count++;
                                $data = array_map([$this , 'assignKeywords'], $data);
                                $filteredData = array_filter($data , function($element){
                                    return !is_null($element['keyword_id']);
                                });
                                DB::table('comments')->insert($filteredData);
                            }
                        }
                    }

                } catch (Exception $e) {
                    dd($e->getMessage());   
                }
            }
        }
    }


    public function Webhook(FacebookWebhook $facebookWebhook)
    {
        if(!is_null($facebookWebhook)){

            if($facebookWebhook->field == 'live_vidoes'){

                $liveVideo = LiveVideo::where('live_vidoe_id' , $facebookWebhook->webhookLiveVideoId);

                if($liveVideo){
                    if($facebookWebhook->webhookLiveVideoStatus == 'live'){
                        $liveVideo->active = 1;
                    }
                    else{
                        $liveVideo->active = 0;
                    }


                    //if the video is not live, remove page app subscription because we don't want to recieve its webhooks anymore
                    $this->removePageSubscription($liveVideo->fb_user_id , $liveVideo->fb_page_id);

                    $liveVideo->status = $facebookWebhook->webhookLiveVideoStatus;
                    $liveVideo->save(); //Update the status
                }

            }
            else if($facebookWebhook->field == 'feed'){

                //then it is for sure that its a comment
                $comment = Comment::where('comment_id' , $facebookWebhook->webhookCommentId)->first();
                if(!$comment){

                    //fetch the post id , post id is something like = 34753498753458394_8394753987539487
                    $postId = $facebookWebhook->webhookCommentPostId;
                    //post_id contains the object_id , and that object_id is the live_video_object_id, after the underscore
                    $postIdArray = explode('_',$postId);
                    //second index will contain the object id
                    $liveVideoObjectId = $postIdArray[1];

                    //get the live_video_id from database table live_videos... because we don't want to change anything else
                    //all the implementation still goes with live_vidoe_id
                    $liveVideo = LiveVideo::where('object_id' , $liveVideoObjectId)->first();

                    if(!is_null($liveVideo)){
                        if($liveVideo->status == 'live')
                        {
                            $commentAuthorExists = $this->commentAuthorExists($facebookWebhook->webhookCommentSenderId, $liveVideo->live_vidoe_id);
                            if(!$commentAuthorExists)
                            {
                                $commentData[0] = array(
                                    'comment_id' => $facebookWebhook->webhookCommentId,
                                    'comment_body' => $facebookWebhook->webhookCommentBody,
                                    'comment_author_id' => $facebookWebhook->webhookCommentSenderId,
                                    'comment_author_name' => $facebookWebhook->webhookCommentSenderName,
                                    'active' => 1,
                                    'keyword_id' => null,
                                    'live_video_id' => $liveVideo->live_vidoe_id
                                );
                                $commentData = array_map([$this , 'assignKeywords'], $commentData); //map keywords
                                $filteredCommentData = array_filter($commentData , function($element){
                                    return !is_null($element['keyword_id']);
                                });
                                DB::table('comments')->insert($filteredCommentData);
                            }
                        }
                    }
                }
            }
        }
        return response('OK',200);
    }

    
    public function commentAuthorExists($commentAuthorId , $liveVideoId) 
    {
        
        $where = ['comment_author_id' => $commentAuthorId , 'live_video_id' => $liveVideoId];
        $comment = Comment::where($where)->first();
        
        if($comment){
            return true;
        }
        else{
            return false;
        }
    }

    private function removePageSubscription($facebookUserId , $facebookPageId){


        $user = User::where('facebook_user_id' , $facebookUserId)->first();

        $this->fb->setDefaultAccessToken($user->access_token);

        try{
            $response = $this->fb->get('/' . $facebookPageId . '?fields=access_token');

            $response = json_decode($response->getBody());

            $this->fb->delete('/' . $facebookPageId . '/subscribed_apps', [] , $response->access_token);

        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }


    }
}
