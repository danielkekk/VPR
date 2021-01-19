<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                position: relative;
                margin: 1700px 5px 15px 20px;
                text-align: left;
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
                <form>
                    @csrf
                    <label for="ev">Év</label>
                    <select name="ev" id="ev">
                        <?php foreach($evek as $ev) { ?>
                        <option value="<?php echo $ev->ev; ?>"><?php echo $ev->ev; ?></option>
                        <?php } ?>
                    </select><br>

                    <label for="honap">Hónap</label>
                    <select name="honap" id="honap">
                        <?php foreach($honapok as $key => $value) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select><br>

                </form><br>

                <span id="error_messages"></span><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Inaktív napok</b></div>
                        <div class="panel-body">
                            <canvas id="inaktiv_napok" height="300" width="600"></canvas>
                        </div>
                    </div>
                </div><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Posztok száma</b></div>
                        <div class="panel-body">
                            <canvas id="posztok_szama" height="300" width="400"></canvas>
                        </div>
                    </div>
                </div><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Reakciók száma</b></div>
                        <div class="panel-body">
                            <canvas id="reakciok_szama" height="300" width="400"></canvas>
                        </div>
                    </div>
                </div><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Követők száma</b></div>
                        <div class="panel-body">
                            <canvas id="kovetok_szama" height="300" width="400"></canvas>
                        </div>
                    </div>
                </div><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Új követők száma</b></div>
                        <div class="panel-body">
                            <canvas id="uj_kovetok_szama" height="300" width="400"></canvas>
                        </div>
                    </div>
                </div><br>

            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.3/js/bootstrap-select.min.js" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
        <script>
            var ctx;
            var myChart;


            function getHaviKimutatas(event) {

                event.preventDefault();

                let url = "{{url('statisztika-havi-kimutatas')}}";
                let inaktivKepviselok = [];
                let inaktivNapok = [];
                let posztokSzamaKepviselok = [];
                let posztokSzama = [];
                let reakciokSzamaKepviselok = [];
                let reakciokSzama = [];
                let kovetokSzamaKepviselok = [];
                let kovetokSzama = [];
                let ujKovetokSzamaKepviselok = [];
                let ujKovetokSzama = [];

                $.ajax({
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'post',
                    contentType: 'application/x-www-form-urlencoded',
                    data: $("form").serialize(),
                    success: function( response, textStatus, jQxhr ) {

                        console.log(""+JSON.stringify(response.response.kovetokSzama));

                        for (const prop in response.response.inaktivNapok) {
                            console.log(""+response.response.inaktivNapok[prop][0]);
                            inaktivKepviselok.push(response.response.inaktivNapok[prop][0]);
                            inaktivNapok.push(response.response.inaktivNapok[prop][1]);
                        }

                        for (const prop in response.response.posztokSzama) {
                            posztokSzamaKepviselok.push(response.response.posztokSzama[prop][0]);
                            posztokSzama.push(response.response.posztokSzama[prop][1]);
                        }

                        for (const prop in response.response.reakciokSzama) {
                            reakciokSzamaKepviselok.push(response.response.reakciokSzama[prop][0]);
                            reakciokSzama.push(response.response.reakciokSzama[prop][1]);
                        }

                        for (const prop in response.response.kovetokSzama) {
                            kovetokSzamaKepviselok.push(response.response.kovetokSzama[prop][0]);
                            kovetokSzama.push(response.response.kovetokSzama[prop][1]);
                        }

                        for (const prop in response.response.ujKovetokSzama) {
                            ujKovetokSzamaKepviselok.push(response.response.ujKovetokSzama[prop][0]);
                            ujKovetokSzama.push(response.response.ujKovetokSzama[prop][1]);
                        }

                        ctx = document.getElementById("inaktiv_napok").getContext('2d');
                        ctx.canvas.width = 600;
                        ctx.canvas.height = 300;
                        myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: inaktivKepviselok,
                                datasets: [{
                                    label: '# of Votes',
                                    data: inaktivNapok,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });


                        ctx = document.getElementById("posztok_szama").getContext('2d');
                        ctx.canvas.width = 400;
                        ctx.canvas.height = 300;
                        myChart = new Chart(ctx, {
                            type: 'horizontalBar',
                            data: {
                                labels: posztokSzamaKepviselok,
                                datasets: [{
                                    label: '# of Votes',
                                    data: posztokSzama,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });

                        ctx = document.getElementById("reakciok_szama").getContext('2d');
                        ctx.canvas.width = 400;
                        ctx.canvas.height = 300;
                        myChart = new Chart(ctx, {
                            type: 'horizontalBar',
                            data: {
                                labels: reakciokSzamaKepviselok,
                                datasets: [{
                                    label: '# of Votes',
                                    data: reakciokSzama,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });


                        ctx = document.getElementById("kovetok_szama").getContext('2d');
                        ctx.canvas.width = 400;
                        ctx.canvas.height = 300;
                        myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: kovetokSzamaKepviselok,
                                datasets: [{
                                    label: '# of Votes',
                                    data: kovetokSzama,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });


                        ctx = document.getElementById("uj_kovetok_szama").getContext('2d');
                        ctx.canvas.width = 400;
                        ctx.canvas.height = 300;
                        myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ujKovetokSzamaKepviselok,
                                datasets: [{
                                    label: '# of Votes',
                                    data: ujKovetokSzama,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });

                    },
                    statusCode: {
                        400: function(responseObject, textStatus, jqXHR) {

                            let txt = "";
                            for(let i=0; i<responseObject.responseJSON.errors.length; i++) {
                                txt += responseObject.responseJSON.errors[i] + "<br>";
                            }

                            $("#error_messages").html(txt);
                            //console.log( responseObject.responseJSON.errors);
                        }
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        console.log( errorThrown );
                    }
                });
            }

            $('#ev').on('change', function(event) {
                getHaviKimutatas(event);
            });

            $('#honap').on('change', function(event) {
                getHaviKimutatas(event);
            });
        </script>
    </body>
</html>
