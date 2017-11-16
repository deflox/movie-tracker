@extends('layouts.app')

@section('content')
    @include('partials.navigation')
    <div class="container">
        <h1>Your Movies <button class="btn btn-primary btn-lg pull-right" id="add-movie">Add new movie</button></h1>
        @include('partials.flash')
        <p>Below you find your watched movies.</p>
        @include('partials.filter')
        <div id="movies">
            @if (count($userMovies) > 0)
                @foreach($userMovies as $userMovie)
                    <div class="movie-image" id="{{ $userMovie->id }}">
                        <img src="https://image.tmdb.org/t/p/w640/{{ $userMovie->movie->imgPath }}" title="{{ $userMovie->movie->title }}">
                    </div>
                @endforeach
            @else
                <p>You don't have any movies yet.</p>
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
                openAddDialog(true);
            });

            $(document).on("click", ".movie-image", function() {
                openShowDialog($(this).attr("id"), false);
            });

            $("#order").change(function() {
                filterMovieList(1, $("#search").val(), $("#order").val());
            });

            $("#search").keyup(function() {
                delay(function(){
                    filterMovieList(1, $("#search").val(), $("#order").val());
                }, 500 );
            });

        });
    </script>
@endsection