@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 first-div">
            <div class="panel panel-default">
                <div class="panel-heading text-center"><h2>THIS WILL BE THE HOME PAGE<h2></div>
                <div class="panel-body text-center">
                    <div id="loading">
                        <img src="{{asset('images/spin.gif')}}">
                    </div>                
                    <form id="campaignForm">
                        <div class="form-group">
                            <label for="campaignName">Campaign Name</label>
                            <input type="text" name="campaignName" id="campaignName" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="keywords">Campaign Keywords</label>
                            <input class="form-control" type="text" data-role="tagsinput" name="keywords" id="keywords">
                        </div>
                        <div class="form-group">
                            <label for="keywords">Select Live Video</label>
                            <select class="form-control" name="liveVideoId">
                                <option value="">SELECT</option>
                                <option value="134234">LIVE VIDEO 1</option>
                                <option value="134234">LIVE VIDEO 1</option>
                            </select>
                            
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success" type="submit" id="campaignFormSubmit">Submit</button>
                        </div>
                    </form>
                    
                </div>
                            
            </div>
            
        </div>
        
        <div class="col-md-8 col-md-offset-2 pages-div">
            <div class="panel panel-default">
                <div class="panel-heading text-center"><h2>SELECT A Facebook Page<h2></div>
                <div class="panel-body text-center">
                    <form id="pagesForm">
                        <div class="form-group">
                            <label for="keywords">Select Live Video</label>
                            <select class="form-control" name="liveVideoId">
                                <option value="">SELECT</option>
                                <option value="134234">LIVE VIDEO 1</option>
                                <option value="134234">LIVE VIDEO 1</option>
                            </select>
                            
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success" type="submit" id="campaignFormSubmit">Submit</button>
                        </div>
                    </form>
                    
                </div>
                            
            </div>
            
        </div>
    </div>
</div>
@endsection