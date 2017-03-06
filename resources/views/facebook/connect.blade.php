@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading text-center"><h2>START CREATING CAMPAIGN<h2></div>
                <div class="panel-body text-center">
                    <a id="loginUrl" href="{{ $loginUrl }}"><img src="{{asset('images/fb.png')}}"> &nbsp;LOGIN WITH FACEBOOK</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection