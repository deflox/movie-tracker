<?php

namespace App\Rules;

use App\Movie;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * Checks if the user already added the given imdb id.
 *
 * @package App\Rules
 */
class IsUniqueMovie implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $movie = Movie::where('imdb_id', $value)
            ->first();

        if ($movie === null) return true;

        $count = $movie->userMovies
            ->where('user_id', Auth::id())
            ->count();

        return ($count === 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The movie already exists either in your movies or your watchlist.';
    }
}
