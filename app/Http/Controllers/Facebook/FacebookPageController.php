<?php

namespace App\Http\Controllers\Facebook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class FacebookPageController extends Controller
{
	private $fb;

	function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$this->fb = $fb;
	}
    //
    public function Index($value='')
    {
    	# code...
    	//Include these lines to each script which makes a call to Graph API
    	$accessToken = Session::get('facbook_access_token');
    	$this->fb->setDefaultAccessToken($accessToken);

    	//Fetch the pages that this user manages
    	$userId = Session::get('fb_user_id');
    	try {
    		$response = $this->fb->get('/'.$userId.'/accounts');
    	} catch (Facebook\Exceptions\FacebookSDKException $e) {
    		dd($e->getMessage());
    	}

    	$pagesEdge = $response->getGraphEdge();
    	$allPagesOfUser = [];
    	$count = 0;
    	foreach ($pagesEdge as $page) {
    		$allPagesOfUser[$count] = $page->asArray();
    		$count++;
	    }
	    return view('facebook.pages',['pages' => $allPagesOfUser]);
    }
}
