@extends('layouts.app')

@section('content')
    @include('partials.navigation')
    <div class="container">
        <h1>Statistics</h1>
        @include('partials.flash')
        <p>Below you find some statistics about your tracked movies.</p>
        <h2>The disappointment:</h2>
        <p>You've spent ...</p>
        <p class="watch-time">{{ $watchTime }}</p>
        <p>... to watch all the added movies.</p>
        <h2>Favorite genres</h2>
        @if (count($favoriteGenres) !== 0)
        <div class="row chart">
            <div class="col-md-6">
                <div class="ct-chart ct-perfect-fourth" id="genres"></div>
            </div>
            <div class="col-md-6">
                <h3>Genres:</h3>
                <ul>
                @foreach ($favoriteGenres as $favoriteGenre)
                    <li>{{ $favoriteGenre->count }} - {{ $favoriteGenre->name }}</li>
                @endforeach
                </ul>
            </div>
        </div>
        @else
            <div class="alert alert-info">
                There is not enough data to generate statistics.
            </div>
        @endif
        <h2>Favorite years</h2>
        @if (count($favoriteYears) !== 0)
        <div class="row chart">
            <div class="col-md-6 text-right">
                <div class="ct-chart ct-perfect-fourth" id="years"></div>
            </div>
            <div class="col-md-6">
                <h3>Years:</h3>
                <ul>
                @foreach ($favoriteYears as $favoriteYear)
                    <li>{{ $favoriteYear->count }} - {{ $favoriteYear->year }}</li>
                @endforeach
                </ul>
            </div>
        </div>
        @else
            <div class="alert alert-info">
                There is not enough data to generate statistics.
            </div>
        @endif
    </div>
@endsection

@section('js')
    <script>
        var genres = {
            series: [{{ $favoriteGenresAsString }}]
        };
        new Chartist.Pie('#genres', genres, {
            labelInterpolationFnc: function(value) {
                return value;
            }
        });

        var years = {
            series: [{{ $favoriteYearsAsString }}]
        };
        new Chartist.Pie('#years', years, {
            labelInterpolationFnc: function(value) {
                return value;
            }
        });
    </script>
@endsection