<?php

namespace App\Http\Controllers\Campaign;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Campaign;
use App\Models\FacebookPage;
use App\Models\LiveVideo;
use App\Models\Comment;

use DB;


class CampaignController extends Controller
{
	private $fb;


	function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$this->fb = $fb;
	}
    //
    public function create(Request $request)
    {
    	# code...
    	$pageId = $request->input('pages-dropdown');
        $pageName = $request->input('pageName');
        Session::put('fb_page_id' , $pageId);

        /////////////////////////////////////////////////////Saving Facebook Page Information///////////////////////
        //Right now, I am doing this here for Initial prototype, but I will move it to proper controller and function
        //and also through gets submitted from AJAX 
        $fbPage = FacebookPage::where('fb_page_id',$pageId)->first();

        if(! $fbPage ){
            //this facebook page does not exist already in our database
            $fbPage = new FacebookPage;
            $fbPage->fb_page_id = $pageId;
            $fbPage->fb_page_name = $pageName;
            $fbPage->fb_user_id = Session::get('fb_user_id');
            $fbPage->active = 1;
            if($fbPage->save()){

            }
            else{
                dd($fbPage);
            }
        }
        else{
            //page id already exists
            $fbPage->fb_page_name = $pageName;
            $fbPage->fb_user_id = Session::get('fb_user_id');
            $fbPage->active = 1;
            if($fbPage->save()){

            }
            else{
                dd($fbPage);
            }
        }
        /////////////////////////////////////// End Saving Facebook Page Information//////////////////////////////////

    	$token = Session::get('facbook_access_token');
    	$this->fb->setDefaultAccessToken($token);

    	try {
    		$response = $this->fb->get('/' . $pageId . '/live_videos');
    	} catch (Facebook\Exceptions\FacebookSDKException $e) {
    		dd($e->getMessage());
    	}

    	$liveVideos = $response->getGraphEdge();
    	$allLiveVideos = array();
    	$count = 0;
    	foreach ($liveVideos as $liveVideo) {
    		$allLiveVideos[$count] = $liveVideo->asArray();
    		$count++;
	    }
    	return view('campaign.create', ['liveVideos' => $allLiveVideos]);
    }


    public function store(Request $request)
    {
        # code...

        $campaign = new Campaign;
        //Move the image file into folder
        $request->file('bg-image')->move(public_path('uploads'), $request->file('bg-image')->getClientOriginalName());

        ///////////////////////////////////////////////Saving Campaign Information//////////////////////////////////////
        $campaign->campaign_name = $request->input('campaignName');
        $campaign->keywords = $request->input('keywords');
        $campaign->image_path = asset('uploads/' . $request->file('bg-image')->getClientOriginalName());
        $campaign->live_video_id = $request->input('live_video_dropdown');
        $campaign->active = 1; //campaign is currently active

        Session::put('live_video_id',$request->input('live_video_dropdown')); 


        if($campaign->save()){
            //get the id of campaign and make a url
            $campaignId = $campaign->id;
            Session::put('campaign_id' , $campaignId);
        }
        else{
            dd($campaign);
        }
        ///////////////////////////////////////////////End Saving Campaign Information//////////////////////////////////////

        ///////////////////////////////////////////////Start Saving Keywords Information//////////////////////////////////////
        $keywords = explode(',' , $request->input('keywords'));
        $count = 0;
        //Unfortunately, i don't know how to do multiple inserts with Eloquent, quite yet! so I will be using query builder here
        $data = array();
        foreach ($keywords as $keyword) {
            # code...
            $data[$count] = array(
                'keyword_name' => $keyword , 
                'campaign_id' => Session::get('campaign_id') ,
                'local_user_id' => Session::get('local_user_id'),
                'live_video_id' => Session::get('live_video_id'),
                'active' => 1
                );
            $count++;
        }

        DB::table('keywords')->insert($data);
        ///////////////////////////////////////////////End Saving Keywords Information//////////////////////////////////////


        ///////////////////////////////////////////////Start Saving Live Video Information//////////////////////////////////////
        $liveVideo = new LiveVideo;
        $liveVideo->live_vidoe_id = Session::get('live_video_id'); //this is a spelling mistake in database
        $liveVideo->live_video_name = $request->input('liveVideoName');
        $liveVideo->active = 1;
        $liveVideo->fb_user_id = Session::get('fb_user_id');
        $liveVideo->fb_page_id = Session::get('fb_page_id');
        $liveVideo->status = $request->input('liveVideoStatus');
        if($liveVideo->save()){

        }
        else{
            dd($liveVideo);
        }
        ///////////////////////////////////////////////End Saving Live Video Information//////////////////////////////////////

        $campaignUrl = action('Campaign\CampaignController@show', ['id' => Session::get('campaign_id')]);
        $campaignUrl .= '/?liveVideo=' . Session::get('live_video_id');
        return view('campaign.liveurl' , ['campaignUrl' => $campaignUrl]);
        //$responseArray  = array('success' => true ,  'url' => $campaignUrl);
        //echo json_encode($responseArray, JSON_UNESCAPED_SLASHES); //this will be helpful for ajax

    }

    public function show($campaignId , Request $request)
    {
        # code...
       $campaign = Campaign::where('id' , $campaignId)->first()->toArray();
       $keywords = explode(',', $campaign['keywords']);
       $boxCount = count($keywords);
       $liveVideoId = $request->query('liveVideo');
       $votes = DB::select('SELECT count(comments.keyword_id) as votes, keywords.keyword_name FROM comments,keywords WHERE comments.live_video_id = 1655266587823356 AND comments.keyword_id = keywords.id GROUP BY comments.keyword_id');
       return view('campaign.live', ['boxCount' => $boxCount , 'votesCount' => $votes , 'imageUrl' => $campaign['image_path']]);
   }
}
