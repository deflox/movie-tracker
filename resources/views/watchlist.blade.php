@extends('layouts.app')

@section('content')
    @include('partials.navigation')
    <div class="container">
        <h1>Your Watchlist <button class="btn btn-primary btn-lg pull-right" id="add-movie">Add new movie</button></h1>
        <p>Below you find your watchlist. This are movies you'd like to watch in the near future.</p>
        @include('partials.filter')
        <div id="movies">
            @if (count($userMovies) > 0)
                @foreach($userMovies as $userMovie)
                    <div class="movie-image" id="{{ $userMovie->id }}">
                        <img src="https://image.tmdb.org/t/p/w640/{{ $userMovie->movie->imgPath }}" title="{{ $userMovie->movie->title }}">
                    </div>
                @endforeach
            @else
                <p>You did not add any movies to your watchlist yet.</p>
            @endif
        </div>
    </div>
    @include('partials.add-modal')
    @include('partials.show-modal')
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            $(document).on("click", "#add-movie", function() {
                openAddDialog(false);
            });

            $(document).on("click", ".movie-image", function() {
                openShowDialog($(this).attr("id"), true);
            });

            $("#order").change(function() {
                filterMovieList(false, $("#search").val(), $("#order").val());
            });

            $("#search").keyup(function() {
                delay(function(){
                    filterMovieList(false, $("#search").val(), $("#order").val());
                }, 500 );
            });

        });
    </script>
@endsection