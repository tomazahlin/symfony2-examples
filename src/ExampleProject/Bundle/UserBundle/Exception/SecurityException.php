<?php

namespace ExampleProject\Bundle\UserBundle\Exception;

/**
 * Class SecurityException
 *
 */
abstract class SecurityException extends BaseException
{
    const TYPE = 'SECURITY';

    /**
     * SecurityException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 401);
    }
}
