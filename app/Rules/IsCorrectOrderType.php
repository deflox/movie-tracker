<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsCorrectOrderType implements Rule
{
    /**
     * The order type
     *
     * @var array
     */
    private $orderingTypes;

    /**
     * Create a new rule instance.
     *
     * @param $orderingTypes
     */
    public function __construct($orderingTypes)
    {
        $this->orderingTypes = $orderingTypes;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ($value <= count($this->orderingTypes));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Incorrect ordering type.';
    }
}
