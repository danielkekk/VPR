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
                    foreach($napiposztok as $napiposzt) {
                        echo '<span style="color: red; font-weight: bold;">' . $napiposzt['tipus'] . '&nbsp;('.$napiposzt['reakcio'].')</span>&nbsp;&nbsp;&nbsp;<a href="'.url("/fra-deletelocalmediaposztbyid/{$localmediaposztid}/{$napiposzt['id']}").'">[X]</a><br>';
                    }
                    ?>

                    <br><br><br>
                <form id="ujlocalmediaposztedit-form" action="{{ url('/fra-editlocalmediaposztsave') }}" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="azon" id="azon" value="<?php echo $localmediaposztid;?>"/>

                    <label for="kovetok_szama">Követők száma</label>
                    <input type="number" name="kovetok_szama" id="reakcio" placeholder="Követők száma" min="0" step="1" required="true" value="<?php echo $kovetokSzama; ?>"/><br>

                    <label for="reakcio">Reakció</label>
                    <input type="number" name="reakcio" id="reakcio" placeholder="Reakciók (db)" min="0"/><br>

                    <label for="poszttipus">Poszt típusa</label>
                    <select name="poszttipus">
                        <?php foreach($poszttipusok as $pt) { ?>
                        <option value="<?php echo $pt->id;?>"><?php echo $pt->web_nev;?></option>
                        <?php } ?>
                    </select><br>

                    <label for="url">URL (link)</label>
                    <input type="text" name="url" id="url" placeholder=""/><br>

                    <input type="submit" value="Új poszt felvitele"/>
                </form>
            </div>
        </div>
    </body>
</html>
