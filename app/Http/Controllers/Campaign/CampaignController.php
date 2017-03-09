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
    
    public function index($value='')
    {
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
        return view('campaign.campaign' , ['pages' => $allPagesOfUser]);
    }
    
    public function saveFacebookPage(Request $request) {
        
        $data = [];
        $errors = [];
        
        $pageId = $request->input('pages-dropdown');
        $pageName = $request->input('pageName');
        Session::put('fb_page_id' , $pageId);
        
        $fbPage = FacebookPage::where('fb_page_id',$pageId)->first();
        
        if(! $fbPage ){
            //this facebook page does not exist already in our database
            $fbPage = new FacebookPage;
            $fbPage->fb_page_id = $pageId;
            $fbPage->fb_page_name = $pageName;
            $fbPage->fb_user_id = Session::get('fb_user_id');
            $fbPage->active = 1;
            if($fbPage->save()){
                $data['success'] = true;
            }
            else{
                $errors['fbPage'] = 'We are having Difficulty Saving Your Facebook Page!';
            }
        }
        else{
            //page id already exists
            $fbPage->fb_page_name = $pageName;
            $fbPage->fb_user_id = Session::get('fb_user_id');
            $fbPage->active = 1;
            if($fbPage->save()){
                $data['success'] = true;
            }
            else{
                $errors['fbPage'] = 'We are having Difficulty Saving Your Facebook Page!';
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
            $pageResponseBody = $page_response->decodeBody();
            if($pageResponseBody['success']){
                $data['subscription'] = true;
            }
            else{
                $errors['subscriptionError'] = "Your selected page couldn't subscribe our App, Make sure you have proper Administrative Rights for this page";
            }
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
    		dd($e->getMessage());
    	}
        $data['errors'] = $errors;
        return response()->json($data);
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
                                 WHERE keywords.campaign_id = ' . $campaignId . '
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
   
   public function store(Request $request){
       $data = [];
       $errors = [];
       $campaignUrl = '';
        
       $campaign = new Campaign;
        if(! $request->input('campaignName')){
            $errors['campaignName'] = 'Campaign Name is Required!';
        }
        else{
            $campaign->campaign_name = $request->input('campaignName');
        }
        if(count($request->input('keywords')) > 4){
            $errors['keywords'] = 'Keyowrds cannot be more than 4';
        }
        else{
            $campaign->keywords = $request->input('keywords');
            
        }
        if($request->file('bg-image')){
            //Move the image file into folder
            $request->file('bg-image')->move(public_path('uploads'), $request->file('bg-image')->getClientOriginalName());
            $campaign->image_path = asset('uploads/' . $request->file('bg-image')->getClientOriginalName());
        }
        else{
            $campaign->image_path = 'https://livotes.com/public/uploads/placeholder.gif';
        }
        
        $campaign->live_video_id = $request->input('live_video_dropdown');
        $campaign->active = 1; //campaign is currently active

        Session::put('live_video_id',$request->input('live_video_dropdown'));
        
        if($campaign->save()){
            //get the id of campaign and make a url
            $campaignId = $campaign->id;
            Session::put('campaign_id' , $campaignId);
            $this->storeKeywords($request->input('keywords'));
            $campaignUrl = action('Campaign\CampaignController@show', ['id' => Session::get('campaign_id')]);
            $campaignUrl .= '/?liveVideo=' . Session::get('live_video_id');
            $campaign->campaign_url = $campaignUrl;
            $campaign->save();
        }
        else{
            $errors['campaignSave'] = 'We are having trouble saving Campaign!';
        }
        if(count($errors)){
            $data['errors'] = $errors;
        }
        else{
            $data['success'] = true;
            $data['url'] = $campaignUrl;
        }

        echo json_encode($data , JSON_UNESCAPED_SLASHES);
   }
   
   private function storeKeywords($keywords) {
       
        $keywords = explode(',' , $keywords);
        $count = 0;
        //Unfortunately, i don't know how to do multiple inserts with Eloquent, quite yet! so I will be using query builder here
        $data = array();
        foreach ($keywords as $keyword) {
            # code...
            $data[$count] = array(
                'keyword_name' => trim($keyword), 
                'campaign_id' => Session::get('campaign_id') ,
                'local_user_id' => Session::get('local_user_id'),
                'live_video_id' => Session::get('live_video_id'),
                'active' => 1
                );
            $count++;
        }

        DB::table('keywords')->insert($data);     
   }

   public function get()
   {
        $campaigns = DB::select('SELECT campaigns.campaign_name, live_videos.live_video_name, campaigns.campaign_url from campaigns,live_videos
                                 where campaigns.live_video_id = live_videos.live_vidoe_id');
        return response()->json($campaigns);
   }

   public function showAll()
   {
       return view('campaign.campaigns');
   }

}
