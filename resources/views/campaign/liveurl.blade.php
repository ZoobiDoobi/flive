@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center"><h2>COPY THIS URL FOR OBS!<h2></div>
                <div class="panel-body text-center">
                    <div class="input-group">
                        <span class="input-group-btn">
                          <button class="btn btn-secondary" type="button">Copy!</button>
                        </span>
                        <input type="text" class="form-control" value="{{ $campaignUrl }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop