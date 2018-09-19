<?php

namespace ExampleProject\Bundle\UserBundle\Exception;

use Exception;

/**
 * Class BaseException
 *
 * The purpose of this exception is to be the base exception of all other exceptions in the application
 *
 * Serialized exceptions would look like this:
 *
 * {
 *   "code": 400,
 *   "type": "VALIDATION",
 *   "message": "Username is already taken by another user."
 * }
 *
 * What if we wanted to return multiple validation errors in the same time... ?
 *
 * Symfony architecture is important, and their event system as well. We should implement an ExceptionListener.
 */
abstract class BaseException extends Exception
{
    // Override this constant in base classes
    const TYPE = 'BASE';

    /**
     * Returns the exception type (string)
     *
     * Frontend can switch between them when multiple exceptions return same http status.
     *
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }
}
