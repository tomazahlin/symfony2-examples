<?php

namespace ExampleProject\Bundle\UserBundle\Security\Authorization\Voter;

use ExampleProject\Bundle\CoreBundle\Constants\Roles;
use ExampleProject\Bundle\CoreBundle\Security\SecurityContextInterface;
use ExampleProject\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter
 *
 * The extended class is deprecated, we should take a look and update the code as necessary.
 *
 * I have used the code from Symfony 2.7, looks like AbstractVoter was deprecated in 2.8.
 *
 * Backwards compatibility is very important, this is why I have used the AbstractVoter here.
 */
class UserVoter extends AbstractVoter
{
    const GET    = 'example_user_get';
    const UPDATE = 'example_user_update';
    const REMOVE = 'example_user_remove';

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return array(
            self::GET,
            self::UPDATE,
            self::REMOVE
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param User $user - The user which is being accessed
     * @param UserInterface $authUser - Currently authenticated user
     *
     * Note the return true and false statements. We only return true when we are sure, the user can do the action.
     * If we returned false when some specific condition is not allowed and true in all the other cases, what would
     * happen if we forget to add some condition?
     */
    protected function isGranted($attribute, $user, $authUser = null)
    {
        if (!$authUser instanceof UserInterface) {

            return false;
        }

        switch ($attribute) {

            case self::GET:
                return true;

            case self::UPDATE:
            case self::REMOVE:
                if ($user === $authUser) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('ExampleProject\Bundle\UserBundle\Entity\User');
    }
}
