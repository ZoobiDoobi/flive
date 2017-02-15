<?php

namespace App\Http\Controllers\Campaign;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Campaign;

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

    	$token = Session::get('facbook_access_token');
    	$this->fb->setDefaultAccessToken($token);

    	try {
    		$response = $this->fb->get('/' . $pageId . '/live_videos');
    	} catch (Facebook\Exceptions\FacebookSDKException $e) {
    		dd($e->getMessage());
    	}

    	$liveVideos = $response->getGraphEdge();
    	$allLiveVideos = [];
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
        //Lets Do the insertions
        $campaign->name = $request->input('campaignName');
        $campaign->keywords = $request->input('keywords');
        $campaign->image_path = asset('uploads/' . $request->file('bg-image')->getClientOriginalName());
        $campaign->live_video_id = $request->input('live_video_dropdown');
        if($campaign->save()){

            //get the id of campaign and make a url
            $campaignId = $campaign->id;
            $campaignUrl = action('Campaign\CampaignController@show', ['id' => $campaignId]);
            $responseArray  = array('success' => true ,  'url' => $campaignUrl);
            echo json_encode($responseArray, JSON_UNESCAPED_SLASHES);
        }  
    }

    public function show($campaignId)
    {
        # code...
        dd($campaignId);
    }
}
