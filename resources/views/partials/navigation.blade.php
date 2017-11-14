<nav>
    <div class="container">
        <div class="row">
            <a href="{{ route('movies') }}">
                <div class="col-md-2 nav-item first">
                    <i class="fa fa-film"></i> Movies
                </div>
            </a>
            <a href="{{ route('watchlist') }}">
                <div class="col-md-2 nav-item">
                    <i class="fa fa-check"></i> Watchlist
                </div>
            </a>
            <a href="{{ route('statistics') }}">
                <div class="col-md-2 nav-item">
                    <i class="fa fa-bar-chart"></i> Stats
                </div>
            </a>
            <a href="{{ route('about') }}">
                <div class="col-md-2 nav-item">
                    <i class="fa fa-info"></i> About
                </div>
            </a>
            <a href="{{ route('settings') }}">
                <div class="col-md-2 nav-item">
                    <i class="fa fa-cog"></i> Settings
                </div>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="col-md-2 nav-item">
                    <i class="fa fa-sign-out"></i> Sign Out
                </div>
            </a>
        </div>
    </div>
</nav>
<form id="logout-form" action="{{ route('logout') }}" method="post" class="hidden">
    {{ csrf_field() }}
</form>