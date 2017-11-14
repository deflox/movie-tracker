<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMovie extends Model
{
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
            ->get();
    }
}
