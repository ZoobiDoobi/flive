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
    <!--     <link href="{{asset('css/app.css')}}" rel="stylesheet">-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/bootstrap.min.css"/>
    <link href="{{asset('css/bootstrap-tagsinput.css')}}" rel="stylesheet">
    <link href="{{asset('css/dropzone.css')}}" rel="stylesheet">
    <link href="{{asset('css/mystyles.css')}}" rel="stylesheet">
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
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
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Livote') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    

    <!-- Scripts -->
    <!--<script src="{{asset('js/app.js')}}"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/alertifyjs/1.9.0/alertify.min.js"></script>
    <script src="{{asset('js/bootstrap-tagsinput.min.js')}}"></script>
    <script src="{{asset('js/dropzone.js')}}"></script>
    <script src="{{asset('js/clipboard.min.js')}}"></script>
    <script src="{{asset('js/myjs.js')}}"></script>
    @yield('js-section')
</body>
</html>
