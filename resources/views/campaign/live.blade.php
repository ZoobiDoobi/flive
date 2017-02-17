<?php
$page = $_SERVER['PHP_SELF'];
$sec = "60";
header("Refresh: $sec; url=$page");
?>
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
   <!--  <link href="css/app.css" rel="stylesheet">-->
   <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <style>
        .col-md-*{
            min-height: 200px !important;
            background: red;
        }
    </style>
</head>
<body style="background: url({{$imageUrl}});">
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                  <!--   <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Livote') }}
                    </a> -->
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>
                </div>
            </div>
        </nav>
    <div class="container">
        <div class="row">
            @if($boxCount == 2)
                <div class="col-md-6"></div>
                <div class="col-md-6"></div>
            @elseif($boxCount == 3)
                <div class="col-md-4">
                    
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            @else
                @for($i = 0; $i < $boxCount; $i++)
                 <div class="col-md-3">
                    <h2>{{$votesCount[$i]->keyword_name}}</h2>
                    <h3>{{$votesCount[$i]->votes}}</h3>
                </div>
                @endfor
            @endif
        </div>
    </div>
    
    </div>

    

    <!-- Scripts -->
    <script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

      
  </script>
</body>
</html>
