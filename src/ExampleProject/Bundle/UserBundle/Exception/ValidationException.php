<?php

namespace ExampleProject\Bundle\UserBundle\Exception;

/**
 * Class ValidationException
 *
 */
final class ValidationException extends BaseException
{
    const TYPE = 'VALIDATION';

    /**
     * ValidationException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 400);
    }
}
