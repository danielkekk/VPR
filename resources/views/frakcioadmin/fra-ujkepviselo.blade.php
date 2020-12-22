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

                <form id="ujkepviselo-form" action="{{ url('/fra-createkepviselo') }}" method="POST">
                    @csrf
                    <label for="kepviselonev">Képviselő neve</label>
                    <input type="text" name="kepviselonev" id="kepviselonev" placeholder="Képviselő neve"/><br>

                    <label for="kepviseloemail">Email</label>
                    <input type="email" name="kepviseloemail" id="kepviseloemail" placeholder="Email cím"/><br>

                    <label for="password">Jelszó</label>
                    <input type="password" name="password" id="password" placeholder="Belépési jelszó"/><br>

                    <label for="password_confirmation">Jelszó mégegyszer</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Belépési jelszó mégegyszer"/><br>

                    <label for="kepviselorole">Jogosultság</label>
                    <select name="kepviselorole">
                        <option value="3">Frakcióvezető</option>
                        <option value="4">Képviselő</option>
                    </select><br>
                    <input type="submit" value="Mentés"/>
                </form>
            </div>
        </div>
    </body>
</html>
