<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="container">
                
                <h1 class="text-center">Start Creating Campaign</h1>
                <div class="row">
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
                            <select name="live_video_dropdown" class="form-control">
                                @foreach($liveVideos as $liveVideo)
                                    <option value="{{ $liveVideo['id']}}">{{ $liveVideo['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bg-image">Select Background Image</label>
                            <input type="file" name="bg-image" id="bg-image">
                        </div><br>
                        <button type="submit">Create Campaign</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
