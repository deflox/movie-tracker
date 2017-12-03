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
                    @include('partials.movie')
                @endforeach
            @else
                <p>You didn't add any movies yet.</p>
            @endif
        </div>
        @if ($totalUserMovies > 24)
            @include('partials.pagination')
        @endif
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

            $(document).on("click", "img", function() {
                openShowDialog($(this).attr("id"), false);
            });

            $("#order").change(function() {
                getMovieList(1, $("#search").val(), $("#order").val());
            });

            $("#search").keyup(function() {
                delay(function(){
                    getMovieList(1, $("#search").val(), $("#order").val());
                }, 500 );
            });

            $(document).on("click", "#previous", function() {
                paginate(1, $("#search").val(), $("#order").val(), $("#page").val(), direction.PREVIOUS);
            });

            $(document).on("click", "#next", function() {
                paginate(1, $("#search").val(), $("#order").val(), $("#page").val(), direction.NEXT);
            });

        });
    </script>
@endsection