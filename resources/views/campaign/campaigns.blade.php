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
                    <table id="campaigns" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Live Video Name</th>
                            <th>Campaign URL</th>
                        </tr>
                        </thead>
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
            var endPointUrl = $('#appUrl');
            $('#campaigns').DataTable( {
                "ajax": endPointUrl + 'campaign/get'
            } );
        } );
    </script>
@stop