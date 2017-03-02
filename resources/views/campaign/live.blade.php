
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
        setTimeout(function(){
           window.location.reload(1);
        }, 5000);
    </script>
    <style>
        .col-md-3{
            min-height: 200px !important;
            background: white;
        }
        .col-md-4{
            min-height: 200px !important;
            background: white;
        }
        .col-md-6{
            min-height: 200px !important;
            background: white;
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
            <div class="row" style="margin-top: 50px;">
                @if($boxCount == 2)
                <div class="col-md-6 text-center" >
                    <h1>{{$votesArray[0]['keyword']}}</h1>
                    <h3>{{$votesArray[0]['votes']}}</h3>
                </div>
                <div class="col-md-6 text-center" >
                    <h1>{{$votesArray[1]['keyword']}}</h1>
                    <h3>{{$votesArray[1]['votes']}}</h3>
                </div>
                @elseif($boxCount == 3)
                <div class="col-md-4 text-center" >
                    <h1>{{$votesArray[0]['keyword']}}</h1>
                    <h3>{{$votesArray[0]['votes']}}</h3>
                </div>
                <div class="col-md-4 text-center" >
                    <h1>{{$votesArray[1]['keyword']}}</h1>
                    <h3>{{$votesArray[1]['votes']}}</h3>
                </div>
                <div class="col-md-4 text-center" >
                    <h1>{{$votesArray[2]['keyword']}}</h1>
                    <h3>{{$votesArray[2]['votes']}}</h3>
                </div>
                @elseif($boxCount == 4)
                <div class="col-md-3 text-center" >
                    <h1>{{$votesArray[0]['keyword']}}</h1>
                    <h3>{{$votesArray[0]['votes']}}</h3>
                </div>
                <div class="col-md-3 text-center" >
                    <h1>{{$votesArray[1]['keyword']}}</h1>
                    <h3>{{$votesArray[1]['votes']}}</h3>
                </div>
                <div class="col-md-3 text-center" >
                    <h1>{{$votesArray[2]['keyword']}}</h1>
                    <h3>{{$votesArray[2]['votes']}}</h3>
                </div>
                <div class="col-md-3 text-center" >
                    <h1>{{$votesArray[3]['keyword']}}</h1>
                    <h3>{{$votesArray[3]['votes']}}</h3>
                </div>
                
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
