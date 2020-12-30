@if (Route::has('login'))
    <div class="top-right links">
        <?php if(Auth::check()) { ?>
            <?php if(Auth::user()->role == 1) { ?>
                <a href="{{ url('/admin') }}">Home</a>
                <a href="{{ url('/loadkepviseloadatokview') }}">Adatok betöltése</a>
                <a href="{{ url('/ujfrakcio') }}">Új frakció</a>
                <!--<a href="{{ url('/ujkepviselo') }}">Új képviselő</a>-->
                <a href="{{ url('/ujogykepviselo') }}">Új ogy. képviselő</a>
                <a href="{{ url('/ujorszmedia') }}">Új orsz. média</a>
                <!--<a href="{{ url('/ujkepviseloposzt') }}">Új képviselőposzt</a>-->
                <a href="{{ url('/ujogykepviseloposzt') }}">Új ogy. képviselőposzt</a>
                <a href="{{ url('/ujorszmediaposzt') }}">Új orsz. média poszt</a>
                <!--<a href="{{ url('/kepviseloposztok') }}">Képviselőposztok</a>-->
                <a href="{{ url('/ogykepviseloposztok') }}">Ogy. képviselőposztok</a>
                <a href="{{ url('/orszmediaposztok') }}">Új orsz.média posztok</a>
            <?php } else if(Auth::user()->role == 2) { ?>
                <a href="{{ url('/fra-ujkepviselo') }}">Új képviselő</a>
                <a href="{{ url('/fra-ujkepviseloposzt') }}">Új képviselőposzt</a>
                <a href="{{ url('/fra-kepviseloposztok') }}">Képviselőposztok</a>
                <a href="{{ url('/fra-ujlocalmedia') }}">Új local média</a>
                <a href="{{ url('/fra-ujlocalmediaposzt') }}">Új local média poszt</a>
                <a href="{{ url('/fra-localmediaposztok') }}">Új local média posztok</a>
            <?php } else if(Auth::user()->role == 3) { ?>
                <a href="{{ url('/statisztika-ogykepviselo') }}">OGY. KÉPVISELŐK</a>
                <a href="{{ url('/statisztika-orszmedia') }}">ORSZÁGOS MÉDIA</a>
                <a href="{{ url('/statisztika-localmedia') }}">HELYI MÉDIA</a>
                <a href="{{ url('/statisztika') }}">KÉPVISELŐK</a>
                <a href="{{ url('/statisztika-havi') }}">HAVI KIMUTATÁS</a>
                <a href="{{ url('/statisztika-frakciovezeto') }}">SAJÁT</a>
            <?php } else if(Auth::user()->role == 4) { ?>
                <a href="{{ url('/a') }}">OGY. KÉPVISELŐK</a>
                <a href="{{ url('/a') }}">ORSZÁGOS MÉDIA</a>
                <a href="{{ url('/a') }}">HELYI MÉDIA</a>
                <a href="{{ url('/a') }}">SAJÁT</a>
            <?php } else { ?>
                <a href="{{ url('/home') }}">Home</a>
             <?php } ?>
            <a href="{{ url('/newpassword') }}">Jelszó módosítása</a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        <?php } else { ?>
            <a href="{{ route('login') }}">Login</a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}">Register</a>
            @endif
        <?php } ?>
    </div>
@endif