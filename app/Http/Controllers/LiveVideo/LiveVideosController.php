<?php

namespace App\Http\Controllers\LiveVideo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\LiveVideo;
use App\Models\Campaign;

class LiveVideosController extends Controller
{
    private $fb;


    function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
            $this->fb = $fb;
    }
    public function getLiveVideos(Request $request){
        
        $data = [];
        $errors = [];
        
        $pageId = Session::get('fb_page_id');
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
                if($liveVideo->getField('status') === 'SCHEDULED_UNPUBLISHED'){
                    
                    $tempArray = $liveVideo->asArray();

                    if(!$this->usedInCampaign($tempArray['id'])){

                        if(!array_key_exists('title', $tempArray)){ //If user somehows skipped the title
                            $tempArray['title'] = $tempArray['id'] . '- Untitled';
                        }

                        $allLiveVideos[$count] = $tempArray;
                        $count++;
                    }
                    
                }	
	}
        if(count($allLiveVideos) <= 0){
            $errors['noLiveVideos'] = 'No Schedule Video On Your Page OR<br>All Scheduled Videos have been used in Campaigns!';
        }
        else{
            $data['success'] = true;
            $this->saveLiveVideos($allLiveVideos , $pageId); //store all scheduled videos
        }
        $data['liveVideos'] = $allLiveVideos;
        $data['errors'] = $errors;
        
        return response()->json($data);
    }
    
    private function saveLiveVideos($liveVideos , $fbPageId){
        
        foreach($liveVideos as $liveVideo){
            
            $liveVideoDb = LiveVideo::where('live_vidoe_id' , $liveVideo['id'])->first();
            
            if($liveVideoDb){ //it already exists in DB so update it
                $liveVideoDb->live_video_name = $liveVideo['title'];
                $liveVideoDb->active = 1;
                $liveVideoDb->fb_user_id = Session::get('fb_user_id');
                $liveVideoDb->fb_page_id = $fbPageId;
                $liveVideoDb->status = $liveVideo['status'];
                $liveVideoDb->save();
            }
            else{
                $liveVideoDb = new LiveVideo; //its not in DB, so create a new instance and save it
                $liveVideoDb->live_vidoe_id = $liveVideo['id']; //this is a spelling mistake in database
                $liveVideoDb->live_video_name = $liveVideo['title'];
                $liveVideoDb->active = 1;
                $liveVideoDb->fb_user_id = Session::get('fb_user_id');
                $liveVideoDb->fb_page_id = $fbPageId;
                $liveVideoDb->status = $liveVideo['status'];
                $liveVideoDb->save();
            }
        }
    }

    private function usedInCampaign($liveVideoId){

        //A campaign with this Live Video has already been created
        $campaign = Campaign::where('live_video_id' , $liveVideoId)->first();

        if($campaign){
            return true;
        }
        else{
            return false;
        }
    }
}
