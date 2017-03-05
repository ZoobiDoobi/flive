<?php

namespace App\Http\Controllers\LiveVideo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\LiveVideo;

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
                    if(!array_key_exists('title', $tempArray)){
                        $tempArray['title'] = $tempArray['id'] . '- Untitled';
                    }
                    $allLiveVideos[$count] = $tempArray;
                    $count++;
                }	
	}
        if(count($allLiveVideos) <= 0){
            $errors['noLiveVideos'] = 'There are no Scheduled Live Videos On Your Page!';
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
                $liveVideoDb->fb_user_id = $fbPageId;
                $liveVideoDb->fb_page_id = Session::get('fb_page_id');
                $liveVideoDb->status = $liveVideo['status'];
                $liveVideoDb->save();
            }
        }
    }
}
