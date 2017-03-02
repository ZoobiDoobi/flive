<?php

namespace App\Http\Controllers\Campaign;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Campaign;
use App\Models\FacebookPage;
use App\Models\LiveVideo;
use App\Models\Comment;
use App\Models\Keyword;

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
        
        //We need to get access token of the page and add our app to {page_id}/subscribed_apps endpoint
        //By sending POST request
        $token = Session::get('facbook_access_token');
    	$this->fb->setDefaultAccessToken($token);
        try{
            $response = $this->fb->get('/' . $pageId . '?fields=access_token');
            $response = json_decode($response->getBody());
            $page_response = $this->fb->post('/' . $pageId . '/subscribed_apps', [] , $response->access_token);
            
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
    		dd($e->getMessage());
    	}
        /////////////////////////////////////// End Saving Facebook Page Information//////////////////////////////////


    	try {
    		$response = $this->fb->get('/' . $pageId . '/live_videos');
              
    	} catch (Facebook\Exceptions\FacebookSDKException $e) {
    		dd($e->getMessage());
    	}

    	$liveVideos = $response->getGraphEdge();
        
    	$allLiveVideos = array();
    	$count = 0;
    	foreach ($liveVideos as $liveVideo) {
                if($liveVideo->getField('status') === 'SCHEDULED_UNPUBLISHED'){
                    
                    $tempArray = $liveVideo->asArray();
                    if(!array_key_exists('title', $tempArray)){
                        $tempArray['title'] = $tempArray['id'] . '- Untitled';
                    }
                    $allLiveVideos[$count] = $tempArray;
                    $count++;
                }
    		
	}
        Session::put('liveVideos', $allLiveVideos);
    	return view('campaign.create', ['liveVideos' => $allLiveVideos]);
    }


    public function store(Request $request)
    {   
        ///////////////////////////////////////////////Saving Campaign Information//////////////////////////////////////
        $campaign = new Campaign;
        $campaign->campaign_name = $request->input('campaignName');
        $campaign->keywords = $request->input('keywords');
        if($request->file('bg-image')){
            //Move the image file into folder
            $request->file('bg-image')->move(public_path('uploads'), $request->file('bg-image')->getClientOriginalName());
            $campaign->image_path = asset('uploads/' . $request->file('bg-image')->getClientOriginalName());
        }
        else{
            $campaign->image_path = 'https://livotes.com/public/uploads/placeholder.png';
        }
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
       $campaign = Campaign::where('id' , $campaignId)->first();
       if($campaign){
            //get keywords of this specific campaign
            $keywords = Keyword::where('campaign_id' , $campaignId)->get();
            $boxCount = count($keywords);
            $liveVideoId = $request->query('liveVideo');
            
            $votes = DB::select('SELECT keywords.keyword_name , COUNT(comments.keyword_id) as votes from keywords
                                 LEFT JOIN comments ON comments.keyword_id =keywords.id 
                                 WHERE keywords.live_video_id = ' . $liveVideoId . '
                                 GROUP BY keywords.id');
            
            $votesArray = [];
            $count = 0;
            if(count($votes)){ //Not empty and commenting has started
                foreach($votes as $vote){
                    $tempArray = array(
                      'keyword' => $vote->keyword_name,
                      'votes' => $vote->votes
                    );
                    $votesArray[$count] = $tempArray;
                    $count++;
                }
            }
            else{
               dd($votes);
            }
           
            return view('campaign.live', ['boxCount' => $boxCount , 'votesArray' => $votesArray , 'imageUrl' => $campaign->image_path ]);
       }
       else{
           echo '<center>Whoops! Looks like this campaign is not <b>ACTIVE</b> anymore!</center>';
       }
       
   }
}
