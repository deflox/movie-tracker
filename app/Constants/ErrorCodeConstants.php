<?php

namespace App\Constants;

/**
 * Constants for error codes which get returned from the internal API.
 *
 * @package App\Constants
 */
class ErrorCodeConstants
{
    /**
     * Error code in case the source of the error is unknown.
     */
    const UNKNOWN_ERROR = "unknown_error";

    /**
     * Error code in case validation errors occurred.
     */
    const VALIDATION_ERROR = "validation_error";

    /**
     * Error code in case the given imdb id does not refer to a movie.
     */
    const IS_NOT_A_MOVIE = "is_not_a_movie";

    /**
     * Error code in case the request type is not valid.
     */
    const INVALID_REQUEST_TYPE = "invalid_request_type";

    /**
     * Error code in case the user has no permissions to the movie.
     */
    const NO_PERMISSIONS = "no_permissions";
}