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

                <form id="ujjelszo-form" action="{{ url('/changepassword') }}" method="POST">
                    @csrf

                    <input type="hidden" name="userid" id="userid" value="<?php echo Auth::user()->id;?>"/>

                    <label for="password">Régi jelszó</label>
                    <input type="password" name="oldpassword" id="oldpassword" placeholder="Régi jelszó" maxlength="30"/><br>

                    <label for="password">Új jelszó</label>
                    <input type="password" name="password" id="password" placeholder="Új jelszó" maxlength="30"/><br>

                    <label for="password_confirmation">Új jelszó mégegyszer</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Új jelszó mégegyszer" maxlength="30"/><br>

                    <input type="submit" value="Mentés"/>
                </form>
            </div>
        </div>
    </body>
</html>
