@extends('layouts.app')

@section('content')
    @include('partials.navigation')
    <div class="container">
        <h1>About this site</h1>
        <p>
            This tool gives you the ability to track your watched movies. Additionally a user can add movies to his watchlist
            which he would like to watch in the future.
        </p>
        <h2>Used technologies</h2>
        <p>
            This site uses the data from <a href="https://www.themoviedb.org" target="_blank">https://www.themoviedb.org</a> which
            offers a very helpful API.
        </p>
        <img src="https://www.themoviedb.org/assets/static_cache/bb45549239e25f1770d5f76727bcd7c0/images/v4/logos/408x161-powered-by-rectangle-blue.png" alt="The Movie DB" style="width: 250px; margin: 5px 0 20px 0">
        <p>
            Additionally following technologies, libraries and frameworks were used to create this site:
        </p>
        <ul>
            <li>PHP / Laravel</li>
            <li>Bootstrap</li>
            <li>BootboxJS</li>
            <li>ChartistJS</li>
            <li>jQuery</li>
            <li>MySQL</li>
        </ul>
        <h2>Issues</h2>
        <p>
            If you encounter any bugs or issues, please report it in the issues section on the
            <a href="https://github.com/deflox/movie-tracker/issues">github repository</a> and I will try to resolve
            the issue as quick as possible. :)
        </p>
    </div>
@endsection