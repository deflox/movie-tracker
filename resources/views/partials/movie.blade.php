@if($userMovie->movie->imgPath !== "default")
    <img src="https://image.tmdb.org/t/p/w640/{{ $userMovie->movie->imgPath }}" title="{{ $userMovie->movie->title }}" alt="{{ $userMovie->movie->title }}" id="{{ $userMovie->id }}">
@else
    <img src="{{ asset('img/default.png') }}" title="{{ $userMovie->movie->title }}" alt="{{ $userMovie->movie->title }}" id="{{ $userMovie->id }}">
@endif