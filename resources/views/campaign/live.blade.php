
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Livotes') }}</title>

    <!-- Styles -->
   <!--  <link href="css/app.css" rel="stylesheet">-->
   <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <style>
        .col-md-3{
            min-height: 200px !important;
            color:white;
            margin-top: 550px;
            
        }
        .col-md-4{
            min-height: 200px !important;
            color:white; 
            margin-top: 550px;
            
        }
        .col-md-6{
            min-height: 200px !important;
            color:white; 
            margin-top: 550px;
              
        }
    </style>
</head>
<body style="background: url({{$imageUrl}});">
    <div id="app">
        <div class="container">
            <div class="row">
                @if($boxCount == 2)
                    <div class="two-boxes">
                        <div class="col-md-6 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[0]['keyword']}}</h1>
                            <h3 id="two-keyword-1-votes" class="keyword-vote">{{$votesArray[0]['votes']}}</h3>
                        </div>
                        <div class="col-md-6 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[1]['keyword']}}</h1>
                            <h3 id="two-keyword-2-votes" class="keyword-vote">{{$votesArray[1]['votes']}}</h3>
                        </div>
                    </div>
                @elseif($boxCount == 3)
                    <div class="three-boxes">
                        <div class="col-md-4 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[0]['keyword']}}</h1>
                            <h3 id="three-keyword-1-votes" class="keyword-vote">{{$votesArray[0]['votes']}}</h3>
                        </div>
                        <div class="col-md-4 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[1]['keyword']}}</h1>
                            <h3 id="three-keyword-2-votes" class="keyword-vote">{{$votesArray[1]['votes']}}</h3>
                        </div>
                        <div class="col-md-4 text-center four-boxes" >
                            <h1 class="keyword-heading">{{$votesArray[2]['keyword']}}</h1>
                            <h3 id="three-keyword-3-votes" class="keyword-vote">{{$votesArray[2]['votes']}}</h3>
                        </div>
                    </div>
                @elseif($boxCount == 4)
                    <div class="four-boxes">
                        <div class="col-md-3 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[0]['keyword']}}</h1>
                            <h3 id="four-keyword-1-votes" class="keyword-vote">{{$votesArray[0]['votes']}}</h3>
                        </div>
                        <div class="col-md-3 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[1]['keyword']}}</h1>
                            <h3 id="four-keyword-2-votes" class="keyword-vote">{{$votesArray[1]['votes']}}</h3>
                        </div>
                        <div class="col-md-3 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[2]['keyword']}}</h1>
                            <h3 id="four-keyword-3-votes" class="keyword-vote">{{$votesArray[2]['votes']}}</h3>
                        </div>
                        <div class="col-md-3 text-center" >
                            <h1 class="keyword-heading">{{$votesArray[3]['keyword']}}</h1>
                            <h3 id="four-keyword-4-votes" class="keyword-vote">{{$votesArray[3]['votes']}}</h3>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>

        var parser = location;

        var endPointUrl = parser.origin;
        var pathName = parser.pathname; //it will give this part -> /campaign/21/
        var regex = /(\d+)/g;
        var campaignId = pathName.match(regex);

        setTimeout(function(){

            $.ajax({

                url : endPointUrl + '/ajaxVotes/' + campaignId + parser.search,
                dataType : 'json',
                method : 'get',
            }).done(function (data, textStatus , jqXHR) {
                if(data.length == 2){
                    //it means there are two keywords and their votes
                    $('.col-md-6').addClass('animated bounceIn');
                    $('#two-keyword-1-votes').empty();
                    $('#two-keyword-1-votes').text(data[0].votes);

                    $('#two-keyword-2-votes').empty();
                    $('#two-keyword-2-votes').text(data[1].votes);
                }
                else if(data.length == 3){
                    //3 keywords and their votes
                    $('.col-md-4').addClass('animated bounceIn');
                    $('#three-keyword-1-votes').empty();
                    $('#three-keyword-1-votes').text(data[0].votes);

                    $('#three-keyword-2-votes').empty();
                    $('#two-keyword-2-votes').text(data[1].votes);

                    $('#three-keyword-3-votes').empty();
                    $('#two-keyword-3-votes').text(data[2].votes);
                }
                else if(data.length == 4){
                    // 4 keywords and their votes
                    $('.col-md-3').addClass('animated bounceIn');
                    $('#four-keyword-1-votes').empty();
                    $('#four-keyword-1-votes').text(data[0].votes);

                    $('#four-keyword-2-votes').empty();
                    $('#four-keyword-2-votes').text(data[1].votes);

                    $('#four-keyword-3-votes').empty();
                    $('#four-keyword-3-votes').text(data[2].votes);

                    $('#four-keyword-4-votes').empty();
                    $('#four-keyword-4-votes').text(data[3].votes);
                }
            }).fail(function (data, errorThrown , jqXHR) {
                alert('failed');
                console.log(data);
                console.log(errorThrown);
                console.log(jqXHR);
            }).always(function (data, textStatus, jqXHR) {
            });
        }, 5000);
    </script>
</body>
</html>
