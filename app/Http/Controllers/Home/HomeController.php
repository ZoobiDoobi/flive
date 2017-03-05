<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    //
    public function index($value='')
    {
    	# code...
    	return view('home.home');
    }
    
    public function campaign(Request $request) {
        
        $errors = [];
        $data = ['success' => true , 'response' => $request->all()];
        
        return response()->json($data);
        if($request->input('files')){
            $this->validate($request, [
               'bg-image' => 'mimes:jpeg,gif,png' 
            ]);
        }
        //Image is valid, store it in uploads folder and copy its path to DB
        $imageFile = $request->input('files')->move(public_path('uploads'), $request->input('bg-image'));
        
        $campaignName = $request->input('campaignName');
        
        if(empty($campaignName)){
            $errors['campaignName'] = 'Campaign Name Cannot Be Empty!';
        }
        
        if(!empty($errors)){
            $data['success'] = FALSE;
            $data['errors'] = $errors;
        }
        else{
            $data['success'] = TRUE;
            $data['message'] = 'success';
        }
        return response()->json($data);
    }
    
    public function imageUpload(Request $request){
        
        return response()->json('success' , 200);
    }
}
