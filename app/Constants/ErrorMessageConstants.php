<?php

namespace App\Constants;

/**
 * Contains error messages which will get returned by the internal api.
 *
 * @package App\Constants
 */
class ErrorMessageConstants
{
    /**
     * Message in case an unknown error occurred.
     */
    const UNKNOWN_ERROR = "An unknown error occurred, please try again later or contact the administrator.";

    /**
     * Message in case of validation errors.
     */
    const VALIDATION_ERRORS = "The entered data is not valid. Please correct it according to the messages and try again.";

    /**
     * Message in case of permission violation.
     */
    const NO_PERMISSION = "You do not have permission for this movie!";
}