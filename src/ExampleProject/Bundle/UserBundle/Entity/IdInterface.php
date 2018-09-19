<?php

namespace ExampleProject\Bundle\UserBundle\Entity;

/**
 * Interface IdInterface
 */
interface IdInterface
{
    /**
     * Persisted entities have their IDs, while entities which are not yet persisted have null.
     *
     * @return int|null
     */
    public function getId();
}
