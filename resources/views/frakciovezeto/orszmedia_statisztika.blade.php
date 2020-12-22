<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.3/css/bootstrap-select.min.css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @include('menu.header_menu')

            <div class="content">
                <label for="orszmedia">Média</label>
                <select name="orszmedia" id="orszmedia">
                    <?php foreach($orszmediak as $orszmedia) { ?>
                    <option value="<?php echo $orszmedia->id; ?>"><?php echo $orszmedia->name; ?></option>
                    <?php } ?>
                </select><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Charts</b></div>
                        <div class="panel-body">
                            <canvas id="canvas" height="300" width="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.3/js/bootstrap-select.min.js" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
        <script>
            var Days = new Array();
            var Labels = new Array();
            var Followers = new Array();
            var ctx;
            var myChart;

            $('#orszmedia').on('change', function() {
                Days = new Array();
                Labels = new Array();
                Followers = new Array();
                url = "{{url('statisztika-orszmedia-poszt')}}" + "/" + $('#orszmedia').val();

                $.get(url, function(response){
                    response.forEach(function(data){
                        Days.push(data.nap);
                        Labels.push(data.datum);
                        Followers.push(data.kovetok_szama);
                    });
                    ctx = document.getElementById("canvas").getContext('2d');
                    ctx.canvas.width = 400;
                    ctx.canvas.height = 300;
                    myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels:Days,
                            datasets: [{
                                label: 'Követők száma',
                                data: Followers,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero:true
                                    }
                                }]
                            }
                        }
                    });
                });
            });
        </script>
    </body>
</html>
