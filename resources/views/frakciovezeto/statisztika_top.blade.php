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
                margin: 300px 5px 15px 20px;
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

                <span id="error_messages"></span><br>

                <b>Top 3 képviselő poszt</b><br>
                <table id="myTableKepviselo"></table><br><br><br>

                <b>Top 3 ogy. képviselő poszt</b><br>
                <table id="myTableOgyKepviselo"></table><br><br><br>

                <b>Top 3 helyi média poszt</b><br>
                <table id="myTableHelyiMedia"></table><br><br><br>

                <b>Top 3 orsz média poszt</b><br>
                <table id="myTableOrszMedia"></table><br><br><br>

            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.3/js/bootstrap-select.min.js" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
        <script>
            var ctx;
            var myChart;

            function getTopKimutatas() {

                //event.preventDefault();

                let url = "{{url('statisztika-top-kimutatas')}}";

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

                        console.log(""+JSON.stringify(response.response_orszmedia_poszt));

                        let table = document.getElementById("myTableKepviselo");
                        while(table.hasChildNodes())
                        {
                            table.removeChild(table.firstChild);
                        }

                        for(let ii=(response.response_kepviselo_poszt.length-1); ii>=0; ii--) {
                            //console.log(JSON.stringify(response.response_kepviselo_poszt[ii]));
                            let rowa = table.insertRow(0);
                            let cell2 = rowa.insertCell(0);
                            let cell3 = rowa.insertCell(0);
                            let cell4 = rowa.insertCell(0);
                            let cell1 = rowa.insertCell(0);
                            cell2.innerHTML = "" + response.response_kepviselo_poszt[ii].reakcio;
                            cell3.innerHTML = "" + response.response_kepviselo_poszt[ii].HM;
                            cell4.innerHTML = "<a href=\""+ response.response_kepviselo_poszt[ii].link +"\" target=\"_blank\">" + response.response_kepviselo_poszt[ii].link + "</a>";
                            cell1.innerHTML = "" + (ii+1);
                        }

                        let rowa = table.insertRow(0);
                        let cell2 = rowa.insertCell(0);
                        let cell3 = rowa.insertCell(0);
                        let cell4 = rowa.insertCell(0);
                        let cell1 = rowa.insertCell(0);
                        cell2.innerHTML = "Reakció";
                        cell3.innerHTML = "HM";
                        cell4.innerHTML = "URL";
                        cell1.innerHTML = "Sorszám";



                        table = document.getElementById("myTableOgyKepviselo");
                        while(table.hasChildNodes())
                        {
                            table.removeChild(table.firstChild);
                        }

                        for(let ii=(response.response_ogykepviselo_poszt.length-1); ii>=0; ii--) {
                            rowa = table.insertRow(0);
                            cell2 = rowa.insertCell(0);
                            cell3 = rowa.insertCell(0);
                            cell4 = rowa.insertCell(0);
                            cell1 = rowa.insertCell(0);
                            cell2.innerHTML = "" + response.response_ogykepviselo_poszt[ii].reakcio;
                            cell3.innerHTML = "" + response.response_ogykepviselo_poszt[ii].HM;
                            cell4.innerHTML = "<a href=\""+ response.response_kepviselo_poszt[ii].link +"\" target=\"_blank\">" + response.response_kepviselo_poszt[ii].link + "</a>";
                            cell1.innerHTML = "" + (ii+1);
                        }

                        rowa = table.insertRow(0);
                        cell2 = rowa.insertCell(0);
                        cell3 = rowa.insertCell(0);
                        cell4 = rowa.insertCell(0);
                        cell1 = rowa.insertCell(0);
                        cell2.innerHTML = "Reakció";
                        cell3.innerHTML = "HM";
                        cell4.innerHTML = "URL";
                        cell1.innerHTML = "Sorszám";



                        table = document.getElementById("myTableHelyiMedia");
                        while(table.hasChildNodes())
                        {
                            table.removeChild(table.firstChild);
                        }

                        for(let ii=(response.response_helyimedia_poszt.length-1); ii>=0; ii--) {
                            rowa = table.insertRow(0);
                            cell2 = rowa.insertCell(0);
                            cell3 = rowa.insertCell(0);
                            cell4 = rowa.insertCell(0);
                            cell1 = rowa.insertCell(0);
                            cell2.innerHTML = "" + response.response_helyimedia_poszt[ii].reakcio;
                            cell3.innerHTML = "" + response.response_helyimedia_poszt[ii].HM;
                            cell4.innerHTML = "<a href=\""+ response.response_kepviselo_poszt[ii].link +"\" target=\"_blank\">" + response.response_kepviselo_poszt[ii].link + "</a>";
                            cell1.innerHTML = "" + (ii+1);
                        }

                        rowa = table.insertRow(0);
                        cell2 = rowa.insertCell(0);
                        cell3 = rowa.insertCell(0);
                        cell4 = rowa.insertCell(0);
                        cell1 = rowa.insertCell(0);
                        cell2.innerHTML = "Reakció";
                        cell3.innerHTML = "HM";
                        cell4.innerHTML = "URL";
                        cell1.innerHTML = "Sorszám";


                        table = document.getElementById("myTableOrszMedia");
                        while(table.hasChildNodes())
                        {
                            table.removeChild(table.firstChild);
                        }

                        for(let ii=(response.response_orszmedia_poszt.length-1); ii>=0; ii--) {
                            //console.log(JSON.stringify(response.response_kepviselo_poszt[ii]));
                            rowa = table.insertRow(0);
                            cell2 = rowa.insertCell(0);
                            cell3 = rowa.insertCell(0);
                            cell4 = rowa.insertCell(0);
                            cell1 = rowa.insertCell(0);
                            cell2.innerHTML = "" + response.response_orszmedia_poszt[ii].reakcio;
                            cell3.innerHTML = "" + response.response_orszmedia_poszt[ii].HM;
                            cell4.innerHTML = "<a href=\""+ response.response_kepviselo_poszt[ii].link +"\" target=\"_blank\">" + response.response_kepviselo_poszt[ii].link + "</a>";
                            cell1.innerHTML = "" + (ii+1);
                        }

                        rowa = table.insertRow(0);
                        cell2 = rowa.insertCell(0);
                        cell3 = rowa.insertCell(0);
                        cell4 = rowa.insertCell(0);
                        cell1 = rowa.insertCell(0);
                        cell2.innerHTML = "Reakció";
                        cell3.innerHTML = "HM";
                        cell4.innerHTML = "URL";
                        cell1.innerHTML = "Sorszám";

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

            $(document).ready(function() {
                console.log('lefut');
                getTopKimutatas();
            });
        </script>
    </body>
</html>
