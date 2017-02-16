<?php

namespace App\Http\Controllers\Facebook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
            if($user->save()){
                Session::put('local_user_id', $user->id);
                return redirect('pages/index');
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
            if($user->save()){
                Session::put('local_user_id', $user->id);
                return redirect('pages/index');
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

}
