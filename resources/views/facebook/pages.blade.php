@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center"><h2>THIS WILL BE THE HOME PAGE<h2></div>
                <div class="panel-body text-center">
                    <form action="{{ action('Campaign\CampaignController@create') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <select name="pages-dropdown" class="pages-dropdown" id="pages" onchange="getSelectedOptionText()">
                            <option value="0">Select..</option>
                            @foreach($pages as $page)
                                <option value="{{ $page['id'] }}"> {{ $page['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="pageName" value="" id="pageName">
                        <button type="submit" id="submitPage">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js-section')
    <script>
        function getSelectedOptionText(){
            var page = document.getElementById('pages');
            var selectedText = page.options[page.selectedIndex].text;
            document.getElementById('pageName').value = selectedText;
        }
   </script>
@stop