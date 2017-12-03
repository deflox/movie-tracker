<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Checks if a imdb id is correctly formatted.
 *
 * @package App\Rules
 */
class ImdbId implements Rule
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
        return (strlen($value) === 9 && preg_match("/tt\\d{7}/", $value) !== 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given IMDb Id is not valid. It must be in following format (example): tt1234567';
    }
}
