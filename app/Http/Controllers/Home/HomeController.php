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
        $data = [];
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
}
