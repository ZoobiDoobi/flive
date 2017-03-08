<?php

namespace App\Http\Controllers\Facebook;

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
                    echo '<pre>';
                    print_r($comments);
                    echo '</pre>';
                    //////////////////////////////Saving Comment/////////////////////////////
                    $data = array();
                    $count = 0;
                    foreach ($comments as $comment) 
                    {    
                        //check if comment already exists in our db
                        $commentExist = $this->commentExists($comment['id']);
                        $authorExist = $this->commentAuthorExists($comment['from']['id'] , $liveVideo->live_vidoe_id);
                        if( $commentExist || $authorExist){
                            echo 'this comment already exists  this author has already commented on this live video!<br>';
                        }
                        else{
                            //this comment does not exist so put it in database please!
                            echo 'This comment does not exist so inserting it!<br>';
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
                            DB::table('comments')->insert($data);
                        }
                    }

                } catch (Exception $e) {
                    dd($e->getMessage());   
                }
            }
        }
    }

    public function Webhook(Request $request)
    {
        # code...
        $verifyToken = 'Axb123xyz';
        if($request->isMethod('get')){
            if($verifyToken == $request->input('hub_verify_token')){
                return $request->input('hub_challenge');
            }
        }
        else if($request->isMethod('post')){
            $liveVideoId = $request->input('entry.0.changes.0.value.id');
            $liveVideoStatus = $request->input('entry.0.changes.0.value.status');
            
            $liveVideo = LiveVideo::where('live_vidoe_id' , $liveVideoId)->first();
            if($liveVideo){
                if($liveVideoStatus == 'live'){
                    $liveVideo->status = $liveVideoStatus;
                    $liveVideo->active = 1; //this video is live and we want to start cron job for this.
                    $liveVideo->save();
                }
                else{
                    $liveVideo->status = $liveVideoStatus;
                    $liveVideo->active = 0;
                    $liveVideo->save();
                }
            }
            else{
                dd($liveVideo);
            }
        }
        return response('OK',200);
    }
    
    public function commentExists($commentId) 
    {
        
        $comment = Comment::where('comment_id' , $commentId)->first();
        
        if($comment){

            return true;
        }
        else{
            return false;
        }
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
}
