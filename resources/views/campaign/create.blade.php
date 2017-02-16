@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center"><h2>THIS WILL BE THE HOME PAGE<h2></div>
                <div class="panel-body text-center">
                        <form action="{{ action('Campaign\CampaignController@store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="campaignName">Campaign Name:</label>
                            <input type="text" name="campaignName" id="campaignName" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="keywords">Keywords:</label>
                            <input type="text" name="keywords" id="keywords" class="form-control" placeholder="Comma Separated Keywords">
                        </div>
                        <div class="form-group">
                            <label for="live_video_dropdown">Select the Live Video:</label>
                            <select name="live_video_dropdown" class="form-control" onChange="getSelectedOptionText(this.selectedIndex);">
                                @foreach($liveVideos as $liveVideo)
                                    <option value="{{ $liveVideo['id']}}">{{ $liveVideo['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bg-image">Select Background Image</label>
                            <input type="file" name="bg-image" id="bg-image">
                        </div>
                        <input type="hidden" name="liveVideoName" id="liveVideoName">
                        <input type="hidden" name="liveVideoStatus" id="liveVideoStatus" value="{{ $liveVideo['status'] }}">
                        <button type="submit" class="btn btn-primary">Create Campaign</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js-section')
    <script>
        function getSelectedOptionText(selectedOption){
            document.getElementById('liveVideoName').value = selectedOption.options[selectedOption.selectedIndex].text;
        }
    </script>
@stop