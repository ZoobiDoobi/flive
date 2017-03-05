@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="facebookPagesPanel">
            <div class="panel panel-primary">
                <div class="panel-heading text-center"><h2>SELECT YOUR FACEBOOK PAGE<h2></div>
                <div class="panel-body text-center">
                    <div id="loading">
                        <img src="{{asset('images/spin.gif')}}">
                        <span class="loading-message"></span>
                    </div>
                    <form id="facebookPagesForm">
                        <select required="required" class="form-control" name="pages-dropdown" class="pages-dropdown" id="pages" onchange="getSelectedPageName()">
                            <option value="">Select..</option>
                            @foreach($pages as $page)
                                <option value="{{ $page['id'] }}"> {{ $page['name'] }}</option>
                            @endforeach
                        </select>
                        <br>
                        <input type="hidden" name="pageName" value="" id="pageName">
                        <button type="submit" id="submitPage" class="btn btn-primary">Submit <i class="glyphicon glyphicon-send"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-md-offset-2" id="createCampaignPanel">
            <div class="panel panel-primary">
                <div class="panel-heading text-center"><h2>CREATE YOUR CAMPAIGN<h2></div>
                <div class="panel-body">
                    <div id="campaignloading">
                        <img src="{{asset('images/spin.gif')}}">
                        <span class="loading-message"></span>
                    </div>                    
                    <form  method="post" enctype="multipart/form-data" id="createCampaignForm" onsubmit="return false">
                        <div class="form-group">
                            <label for="campaignName">Campaign Name:</label>
                            <input type="text" name="campaignName" id="campaignName" class="form-control" required="required">
                        </div>
                        
                        <div class="form-group">
                            <label for="live_video_dropdown">Select the Live Video:</label>
                            <select name="live_video_dropdown" id="videos" class="form-control" required="required">
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keywords">Keywords:</label><br>
                            <input data-role="tagsinput" type="text" name="keywords" id="keywords" class="form-control" placeholder="Comma Separated Keywords" required="required">
                        </div>
                        <div class="form-group">
                            <label for="bg-image">Select Background Image</label>
                            <input type="file" name="bg-image" id="bg-image">
                        </div>
                        <input type="button" id="createCampaignBtn" class="btn btn-primary" value="Create Campaign">
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 col-md-offset-2" id="campaignUrl">
            <div class="panel panel-primary">
                <div class="panel-heading text-center"><h2>COPY THIS URL FOR OBS!<h2></div>
                <div class="panel-body text-center">
                    <div class="input-group">
                        <span class="input-group-btn">
                          <button id="clipBoardButton" class="btn btn-secondary" type="button" data-clipboard-target="urlTextBox">Copy!</button>
                        </span>
                        <input id="urlTextBox" type="text" class="form-control" value="" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop


@section('js-section')
    <script>
        function getSelectedPageName(){
            var page = document.getElementById('pages');
            var selectedText = page.options[page.selectedIndex].text;
            document.getElementById('pageName').value = selectedText;
        }
   </script>
@stop