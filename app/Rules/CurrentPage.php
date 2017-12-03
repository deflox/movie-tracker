<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CurrentPage implements Rule
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
        if (!isset($vaue)) return true;

        return ($value > 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Incorrect current page value.';
    }
}
