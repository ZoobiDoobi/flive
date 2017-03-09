@extends('layouts.app')

@section('css-section')
    <link href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="facebookPagesPanel">
            <div class="panel panel-primary">
                <div class="panel-heading text-center"><h2>ALL CAMPAIGNS</h2></div>
                <div class="panel-body text-center">
                    <div id="loading">
                        <img src="{{asset('images/spin.gif')}}">
                        <span class="loading-message"></span>
                    </div>
                    <table id="campaigns" class="display" cellspacing="0" width="100%">
                        <th>Campaign Name</th>
                        <th>Live Video Name</th>
                        <th>Campaign URL</th>
                    </table>
                </div>
            </div>
        </div>
        <div id="appUrl" style="display: none;">{{ url('/') }}</div>
    </div>
</div>
@stop

@section('js-section')
    <script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script>

        $(document).ready(function() {
            var endPointUrl = $('#appUrl').text();
            $.ajax({
                url : endPointUrl + '/campaign/get',
                method : 'GET',
                dataType : 'json',
                beforeSend : function(){
                    $('#loading').css('display' , 'block');
                    $('#campaigns').css('display' , 'none');
                }
            }).done(function(data , textStatus, jqXHR){
                $('#loading').css('display' , 'none');
                $('#campaigns').css('display' , 'block');

                $('#campaigns').DataTable({
                    data : data,
                    "columns" :[
                        { "data": "campaign_name" },
                        { "data": "live_video_name" },
                        { "data": "campaign_url" },
                    ]
                });
            }).fail(function(data, textStatus, errorThrown){
                $('#loading').css('display' , 'none');
                $('#campaigns').css('display' , 'block');
                console.log(errorThrown);
            }).always(function(data, textStatus, jqXHR){
                $('#loading').css('display' , 'none');
            });
        } );
    </script>
@stop