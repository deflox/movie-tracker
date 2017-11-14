<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'imdb_id',
        'themoviedb_id',
        'title',
        'plot',
        'runtime',
        'year',
        'imgPath'
    ];

    /**
     * Get the user movies for the movie.
     */
    public function userMovies()
    {
        return $this->hasMany('App\UserMovie');
    }

    /**
     * The genres that belong to the movie.
     */
    public function genres()
    {
        return $this->belongsToMany('App\Genre');
    }

    /**
     * Returns the list of genres of this movie represented as a string and comma separated
     * list.
     */
    public function genresAsString()
    {
        $output = '';
        $counter = 0;
        $genres = $this->genres;
        foreach ($genres as $genre) {
            $output .= $genre->name;
            if ($counter < count($genres)-1) $output .= ', ';
            $counter++;
        }
        return $output;
    }
}
