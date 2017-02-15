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
    //
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

        //Put in the session for later use
        Session::put('facbook_access_token', $token);
        //Getting user specific information and store it in databse using Eloquent User Model
        try {
            $response = $this->fb->get('/me?fields=id,name,email');
            $facebookUser = $response->getGraphUser();
            // Create the user if it does not exist or update the existing entry.

        } catch (Exception $e) {
            dd($e->getMessage());
        }

        $userNode = $response->getGraphUser();
        Session::put('fb_user_id', $userNode->getId()); // put the user id in session, we would retrieve 

        //redirect the user, so user can see a list of the pages that he/she manages

        return redirect('pages/index');
    }

    public function Webhook(Request $request)
    {
        # code...
        if($request->isMethod('get')){
            //Below code is just for verification for the first time
            $verifyToken = 'Axb123xyz';
            if($request->input('hub_verify_token') == $verifyToken){
                return $request->input('hub_challenge');
            } 
        }
        elseif ($request->isMethod('post')) {
            Storage::disk('local')->put('webhook.txt', $request->input('entry.0.changes.0.value.id'));
             //$requestArray = json_encode($request->all());
             //$requestArray = json_decode($requestArray,  true, 512, JSON_BIGINT_AS_STRING);
             //Storage::disk('local')->put('webhook.txt', print_r($requestArray, true));
        }
        
        
        return response('OK',200);
    }

    public function test($value='')
    {
        # code...
        
    }

}
