<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

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
                <?php
                    if(isset($errors)) {
                        foreach ($errors->all() as $message) {
                            echo '<span style="color: red; font-weight: bold;">' . $message . '</span><br>';
                        }
                    }
                ?>
                <br><br><br>

                <?php
                if(isset($kepviseloposztok)) {
                    foreach ($kepviseloposztok as $kp) {
                        echo '<span style="color: red; font-weight: bold;">' . $kp->name . '['.$kp->kovetok_szama.']&nbsp;('.$kp->ev.'-'.$kp->honap.'-'.$kp->nap.')</span>&nbsp;&nbsp;&nbsp;<a href="'.url("/editkepviseloposzt/{$kp->id}").'">[SZ]</a>&nbsp;&nbsp;<a href="'.url("/deletekepviseloposzt/{$kp->id}").'">[X]</a><br>';
                    }
                }
                ?>

                <!--<br><br><br>
                <form id="ujfrakcio-form" action="{{ url('/createfrakcio') }}" method="POST">
                    @csrf

                    <label for="name">Frakció neve</label>
                    <input type="text" name="name" id="name" placeholder="Frakció neve"/><br>

                    <label for="code">Frakció kódja</label>
                    <input type="text" name="code" id="code" placeholder="Frakció kódja"/><br>

                    <label for="varos">Város</label>
                    <input type="text" name="varos" id="varos" placeholder="Város"/><br>


                    <input type="submit" value="Mentés"/>
                </form>-->
            </div>
        </div>
    </body>
</html>
