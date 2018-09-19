<?php

namespace ExampleProject\Bundle\UserBundle\Exception;

/**
 * Class LogicException
 *
 */
abstract class LogicException extends BaseException
{
    const TYPE = 'LOGIC';

    /**
     * LogicException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 400);
    }
}
