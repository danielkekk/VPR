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

                <form id="loadkepviseloadatok-form" action="{{ url('/loadkepviseloadatok') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label for="poszttype">Típus</label>
                    <select name="poszttype">
                        <?php foreach($tipusok as $key => $value) { ?>
                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                        <?php } ?>
                    </select><br><br>

                    <label for="orszmedia">Országos média</label>
                    <select name="orszmedia">
                        <?php foreach($orszagosmediak as $media) { ?>
                        <option value="<?php echo $media->id;?>"><?php echo $media->name;?></option>
                        <?php } ?>
                    </select><br>

                    <label for="helyimedia">Helyi média</label>
                    <select name="helyimedia">
                        <?php foreach($helyimediak as $media) { ?>
                        <option value="<?php echo $media->id;?>"><?php echo $media->name;?></option>
                        <?php } ?>
                    </select><br>

                    <label for="ogykepviselo">Ogy. Képviselő</label>
                    <select name="ogykepviselo">
                        <?php foreach($ogykepviselok as $kepviselo) { ?>
                        <option value="<?php echo $kepviselo->id;?>"><?php echo $kepviselo->name;?></option>
                        <?php } ?>
                    </select><br>

                    <label for="kepviselo">Képviselő</label>
                    <select name="kepviselo">
                        <?php foreach($kepviselok as $kepviselo) { ?>
                        <option value="<?php echo $kepviselo->id;?>"><?php echo $kepviselo->name;?></option>
                        <?php } ?>
                    </select><br>

                    <label for="file">Képviselő adatok</label>
                    <input type="file" name="filecsv" id="filecsv" accept=".csv" /><br>

                    <input type="submit" value="Feltöltés"/>
                </form>
            </div>
        </div>
    </body>
</html>
