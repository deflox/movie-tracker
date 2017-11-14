<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller for handling all statistics.
 *
 * @package App\Http\Controllers
 */
class StatisticsController extends Controller
{
    /**
     * Shows the statistics page.
     */
    public function index()
    {
        $favoriteGenres = $this->getFavoriteGenres();
        $favoriteYears = $this->getFavoriteYears();

        return view('statistics', [
            'watchTime' => $this->getWatchTime(),
            'favoriteGenres' => $favoriteGenres,
            'favoriteGenresAsString' => $this->favoritesAsString($favoriteGenres),
            'favoriteYears' => $favoriteYears,
            'favoriteYearsAsString' => $this->favoritesAsString($favoriteYears),
        ]);
    }

    /**
     * Returns the string representation of the total time needed to watch all movies
     * the user added to his list.
     *
     * @return string
     */
    private function getWatchTime()
    {
        $sum = DB::table('user_movies')
            ->join('movies', 'user_movies.movie_id', '=', 'movies.id')
            ->where('user_movies.user_id', Auth::id())
            ->sum('movies.runtime');

        if ($sum <= 60) {
            return $sum . ' minutes';
        }
        if ($sum <= 1440) {
            $hours = floor($sum / 60);
            return $hours . ' hours and ' . ($sum - ($hours*60)) . ' minutes';
        }
        $days = floor($sum / 1440);
        $hours = floor(($sum - ($days * 1440)) / 60);
        return $days . ' days, ' . $hours . ' hours and ' . ($sum - ($days * 1440 + $hours * 60)) . ' minutes';
    }

    /**
     * Returns an array with the count of the users favorite genres limited to five
     * entries.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getFavoriteGenres()
    {
        return DB::table('user_movies')
            ->join('movies', 'user_movies.movie_id', '=', 'movies.id')
            ->join('genre_movie', 'genre_movie.movie_id', '=', 'movies.id')
            ->join('genres', 'genre_movie.genre_id', '=', 'genres.id')
            ->where('user_movies.user_id', Auth::id())
            ->where('user_movies.watched', 1)
            ->select(DB::raw('count(*) as count'), 'genres.name')
            ->groupBy('genres.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Returns an array with the count of the users favorite years limited to five
     * entries.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getFavoriteYears()
    {
        return DB::table('user_movies')
            ->join('movies', 'user_movies.movie_id', '=', 'movies.id')
            ->where('user_movies.user_id', Auth::id())
            ->where('user_movies.watched', 1)
            ->select(DB::raw('count(*) as count'), 'movies.year')
            ->groupBy('movies.year')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Gets the count value of the favorites arrays and puts them together in a string.
     *
     * @param  $favorites
     * @return string
     */
    private function favoritesAsString($favorites)
    {
        $counter = 1;
        $output = '';
        foreach ($favorites->pluck('count')->toArray() as $count) {
            $output .= $count;
            if ($counter !== count($favorites)) $output .= ',';
            $counter++;
        }
        return $output;
    }
}