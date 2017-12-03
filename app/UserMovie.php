<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMovie extends Model
{
    /**
     * Defines how many movies are displayed on the each page.
     */
    const LIMIT = 24;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_movies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'watched',
        'user_id',
        'movie_id',
    ];

    /**
     * Get the movie that owns the user movie.
     */
    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }

    /**
     * Returns all watched movies for the current logged in user.
     *
     * @return mixed
     */
    public static function watchedMovies()
    {
        return self::where('user_id', Auth::id())
            ->where('watched', 1)
            ->orderBy('created_at', 'desc')
            ->limit(UserMovie::LIMIT)
            ->get();
    }

    /**
     * Returns all unwatched movies for the current logged in user.
     *
     * @return mixed
     */
    public static function unwatchedMovies()
    {
        return self::where('user_id', Auth::id())
            ->where('watched', 0)
            ->orderBy('created_at', 'desc')
            ->limit(UserMovie::LIMIT)
            ->get();
    }

    /**
     * Gets the total watched or unwatched user movies for the current
     * logged in user.
     *
     * @param  $watched
     * @return integer
     */
    public static function getTotalUserMoviesForUser($watched)
    {
        return self::where('user_id', Auth::id())
            ->where('watched', $watched)
            ->get()
            ->count();
    }
}
