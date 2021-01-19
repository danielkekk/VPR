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
                margin: 1000px 5px 15px 20px;
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

                    <label for="kepviselo">Képviselő</label>
                    <select name="kepviselo" id="kepviselo">
                        <?php foreach($kepviselok as $kepviselo) { ?>
                        <option value="<?php echo $kepviselo->id; ?>"><?php echo $kepviselo->name; ?></option>
                        <?php } ?>
                    </select>
                </form><br>

                <span id="error_messages"></span><br>

                <b>Statisztikák</b><br>
                <table id="myTable"></table><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Poszt típusok</b></div>
                        <div class="panel-body">
                            <canvas id="canvas" height="300" width="400"></canvas>
                        </div>
                    </div>
                </div><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Követők száma</b></div>
                        <div class="panel-body">
                            <canvas id="canvas_kovetok_szama" height="300" width="600"></canvas>
                        </div>
                    </div>
                </div><br>

                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Megosztási hatékonyság</b></div>
                        <div class="panel-body">
                            <canvas id="canvas_megosztasi_hatekonysag" height="300" width="600"></canvas>
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
            var Days = new Array();
            var Labels = new Array();
            var Followers = new Array();
            var ctx;
            var myChart;

            $('#kepviselo').on('click', function(event) {
                event.preventDefault();

                Days = new Array();
                Labels = new Array();
                Followers = new Array();
                url = "{{url('kepviselo-poszt')}}";

                $.ajax({
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'post',
                    contentType: 'application/x-www-form-urlencoded',
                    data: $("form").serialize(),
                    success: function( response, textStatus, jQxhr ){

                        //ide a kepviselo adatokat
                        //felépítjük a táblázatot
                        let table = document.getElementById("myTable");
                        while(table.hasChildNodes())
                        {
                            table.removeChild(table.firstChild);
                        }
                        let rowa = table.insertRow(0);
                        let cella = rowa.insertCell(0);
                        cella.innerHTML = "Követők száma: " + response.kepviselo_datas.kovetok_szama;
                        cella.colSpan = "9";
                        let rowb = table.insertRow(0);
                        let cellb = rowb.insertCell(0);
                        cellb.innerHTML = "Új követők: " + response.kepviselo_datas.uj_kovetok;
                        cellb.colSpan = "9";

                        let row = table.insertRow(0);
                        let cell1 = row.insertCell(0);
                        cell1.innerHTML = "Átlagos hatékonyság: " + response.kepviselo_datas.sum_atlag_hm;
                        cell1.colSpan = "9";
                        let row1 = table.insertRow(0);
                        let cell2 = row1.insertCell(0);
                        cell2.innerHTML = "Átlag napi poszt: " + response.kepviselo_datas.atlag_napi_poszt;
                        cell2.colSpan = "9";
                        let row2 = table.insertRow(0);
                        let cell3 = row2.insertCell(0);
                        cell3.innerHTML = "Inaktív napok száma: " + response.kepviselo_datas.inaktiv_napok;
                        cell3.colSpan = "9";
                        let row3 = table.insertRow(0);
                        let cell4 = row3.insertCell(0);
                        cell4.innerHTML = "Reakciók: " + response.kepviselo_datas.sum_reakciok;
                        cell4.colSpan = "9";
                        let row4 = table.insertRow(0);
                        let cell5 = row4.insertCell(0);
                        let cell6 = row4.insertCell(1);
                        let cell7 = row4.insertCell(2);
                        let cell8 = row4.insertCell(3);
                        let cell9 = row4.insertCell(4);
                        let cell10 = row4.insertCell(5);
                        let cell10a = row4.insertCell(6);
                        let cell10b = row4.insertCell(7);
                        cell5.innerHTML = "" + response.kepviselo_datas.sum_poszt;
                        cell6.innerHTML = "" + response.kepviselo_datas.sum_sajat;
                        cell6.innerHTML = "" + response.kepviselo_datas.sum_szemelyes;
                        cell7.innerHTML = "" + response.kepviselo_datas.sum_alpolg;
                        cell8.innerHTML = "" + response.kepviselo_datas.sum_polg;
                        cell9.innerHTML = "" + response.kepviselo_datas.sum_csoportoldal;
                        cell10.innerHTML = "" + response.kepviselo_datas.sum_media;
                        cell10a.innerHTML = "" + response.kepviselo_datas.sum_kepviselotars;
                        cell10b.innerHTML = "" + response.kepviselo_datas.sum_egyeb;
                        let row5 = table.insertRow(0);
                        let cell11 = row5.insertCell(0);
                        let cell12 = row5.insertCell(1);
                        let cell13 = row5.insertCell(2);
                        let cell14 = row5.insertCell(3);
                        let cell15 = row5.insertCell(4);
                        let cell16 = row5.insertCell(5);
                        let cell17 = row5.insertCell(6);
                        let cell18 = row5.insertCell(7);
                        let cell19 = row5.insertCell(8);
                        cell11.innerHTML = "Összes poszt";
                        cell12.innerHTML = "Saját";
                        cell13.innerHTML = "Személyes";
                        cell14.innerHTML = "Alpolgármesteri";
                        cell15.innerHTML = "Polgármesteri";
                        cell16.innerHTML = "Csoportoldal";
                        cell17.innerHTML = "Média";
                        cell18.innerHTML = "Képviselőtárs";
                        cell19.innerHTML = "Egyéb";

                        ctx = document.getElementById("canvas").getContext('2d');
                        ctx.canvas.width = 400;
                        ctx.canvas.height = 300;
                        myChart = new Chart(ctx, {
                            type: 'polarArea',
                            data: {
                                datasets: [{
                                    data: [response.kepviselo_datas.sum_sajat,
                                        response.kepviselo_datas.sum_szemelyes,
                                        response.kepviselo_datas.sum_polg,
                                        response.kepviselo_datas.sum_alpolg,
                                        response.kepviselo_datas.sum_csoportoldal,
                                        response.kepviselo_datas.sum_media,
                                        response.kepviselo_datas.sum_kepviselotars,
                                        response.kepviselo_datas.sum_egyeb
                                    ]
                                }],
                                labels: [
                                    'Saját',
                                    'Személyes',
                                    'Polgármesteri',
                                    'Alpolgármesteri',
                                    'Csoportoldal',
                                    'Média',
                                    'Képviselőtárs',
                                    'Egyéb',
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                            }
                        });



                        /*response.post_datas.forEach(function(data){
                            Days.push(data.nap);
                            Labels.push(data.datum);
                            Followers.push(data.kovetok_szama);
                        });*/
                        ctx = document.getElementById("canvas_kovetok_szama").getContext('2d');
                        ctx.canvas.width = 600;
                        ctx.canvas.height = 300;
                        let kovSzamaLabels = [];
                        let kovSzamaValues = [];


                       for (const prop in response.post_datas) {
                            console.log(": " + JSON.stringify(response.post_datas[prop][1]));
                            kovSzamaLabels.push(response.post_datas[prop][0]);
                            kovSzamaValues.push(response.post_datas[prop][1]);
                        }

                        myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: kovSzamaLabels,
                                datasets: [{
                                    label: 'Követők száma',
                                    fill: false,
                                    data: kovSzamaValues,
                                    yAxisID: 'y-axis-1',
                                    lineTension: 0.1
                                }]
                            },
                            options: {
                                responsive: true,
                                hoverMode: 'index',
                                stacked: false,
                                title: {
                                    display: true,
                                    text: 'Követők száma'
                                },
                                scales: {
                                    yAxes: [{
                                        type: 'linear',
                                        display: true,
                                        position: 'left',
                                        id: 'y-axis-1',
                                    }],
                                }
                            }
                        });



                        ctx = document.getElementById("canvas_megosztasi_hatekonysag").getContext('2d');
                        ctx.canvas.width = 600;
                        ctx.canvas.height = 300;
                       // let kovSzamaLabels = [];
                        //let kovSzamaValues = [];


                        /*for (const prop in response.post_datas) {
                            console.log(": " + JSON.stringify(response.post_datas[prop][1]));
                            kovSzamaLabels.push(response.post_datas[prop][0]);
                            kovSzamaValues.push(response.post_datas[prop][1]);
                        }*/
                        myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                                datasets: [{
                                    type: 'bar',
                                    label: 'Megosztás',
                                    backgroundColor: '#fff333',
                                    data: [
                                        300,
                                        400,
                                        500,
                                        600,
                                        4232,
                                        400,
                                        500,
                                    ],
                                    borderColor: 'white',
                                    borderWidth: 2
                                }, {
                                    type: 'line',
                                    label: 'Reakciók',
                                    borderColor: '#999933',
                                    borderWidth: 2,
                                    fill: false,
                                    data: [
                                        1000,
                                        2000,
                                        3000,
                                        4000,
                                        5000,
                                        4000,
                                        5000
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                title: {
                                    display: true,
                                    text: 'Megosztási hatékonyság'
                                },
                                tooltips: {
                                    mode: 'index',
                                    intersect: true
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
            });
        </script>
    </body>
</html>
