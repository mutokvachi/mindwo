<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use \App\Exceptions;

    /**
     * Factory for groups publishing validators
     */
    class ValidatorFactory
    {

        public static function build_validator($code, $group)
        {
            $class = "App\\Http\\Controllers\\Calendar\\Validators\Validator_" . $code;

            if (!class_exists($class)) {
                throw new Exceptions\DXCustomException(trans('errors.publish_validator_not_exists', ['code' => $code]));
            }

            return new $class($group);
        }

    }

}