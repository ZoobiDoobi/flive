@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center"><h2>Fill Out Below Form to Create Your Campaign<h2></div>
                <div class="panel-body text-center">
                        <form action="{{ action('Campaign\CampaignController@store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="campaignName">Campaign Name:</label>
                            <input type="text" name="campaignName" id="campaignName" class="form-control" required="required">
                        </div>
                        <div class="form-group">
                            <label for="keywords">Keywords:</label>
                            <input data-role="tagsinput" type="text" name="keywords" id="keywords" class="form-control" placeholder="Comma Separated Keywords" required="required">
                        </div>
                        <div class="form-group">
                            <label for="live_video_dropdown">Select the Live Video:</label>
                            <select name="live_video_dropdown" id="videos" class="form-control" onchange="getSelectedOptionText()" required="required">
                                <option value="">Select a Live Video</option>
                                @foreach($liveVideos as $liveVideo)
                                    <option value="{{ $liveVideo['id']}}" data-status="{{ $liveVideo['status'] }}"> {{ $liveVideo['title'] }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bg-image">Select Background Image</label>
                            <input type="file" name="bg-image" id="bg-image">
                        </div>
                        <input type="hidden" name="liveVideoName" id="liveVideoName">
                        <input type="hidden" name="liveVideoStatus" id="liveVideoStatus">
                        <button type="submit" class="btn btn-primary">Create Campaign</button>
                    </form>
                </div>
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


@section('js-section')
    <script>
        function getSelectedOptionText(){
            var liveVideo = document.getElementById('videos');
            var selectedText = liveVideo.options[liveVideo.selectedIndex].text;
            var liveVideoStatus = liveVideo.options[liveVideo.selectedIndex].getAttribute('data-status');
            document.getElementById('liveVideoName').value = selectedText;
            document.getElementById('liveVideoStatus').value = liveVideoStatus;
        }
   </script>
@stop