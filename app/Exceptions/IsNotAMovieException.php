<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception in case the given imdb id does not refer to a movie.
 *
 * @package App\Exceptions
 */
class IsNotAMovieException extends Exception
{
    /**
     * IsNotAMovieException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}