<?php

namespace ExampleProject\Bundle\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * Can extend an abstract controller, if we want to avoid injecting same dependencies all the time.
 * 
 * But we do not have to extend Symfony's Controller.
 */
class UserController
{
    /**
     * Get one user (Returns an User)
     *
     * @Nelmio\ApiDoc(
     *  section="User",
     *  resource=true,
     *  requirements={
     *      {"name"="user_id", "description"="User ID"}
     *  },
     *  output={
     *      "class"="ExampleProject\Bundle\UserBundle\Entity\User",
     *      "groups"={"alwaysGet", "standardGet"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when authorization fails",
     *      404="Returned when resource not found"
     *  }
     * )
     *
     * @param User $user
     * @Config\ParamConverter("user", converter="user")
     * @return User
     */
    public function getAction(User $user)
    {
        // Step 1: Authentication
        $this->authentication->isGranted(UserVoter::GET, $user);

        // Optional
        $this->eventDispatcher->dispatch(UserEvents::VISIT, new VisitEvent($user, $this->getUser()));

        // Return the result
        return $user;
    }

    /**
     * Remove user
     *
     * @Nelmio\ApiDoc(
     *  section="User",
     *  resource=true,
     *  requirements={
     *      {"name"="user_id", "description"="User ID"}
     *  },
     *  input={
     *      "class"="ExampleProject\Bundle\UserBundle\Model\Deletion",
     *      "groups"={"deletion"}
     *  },
     *  statusCodes={
     *      204="Returned when successful",
     *      401="Returned when authorization fails",
     *      404="Returned when resource not found"
     *  }
     * )
     *
     * @param User $user
     * @Config\ParamConverter("user", converter="user")
     * @return void
     */
    public function removeAction(User $user)
    {
        // Step 1: Authentication
        $this->authentication->isGranted(UserVoter::REMOVE, $user);

        // Step 2: Delegate business logic to other classes

        // Maybe it would be good if the deletion is wrapped in a transaction

        $this->userManager->delete($user);

        // Optional
        $this->eventDispatcher->dispatch(UserEvents::DELETION, new DeletionEvent($user));

        // Return nothing, a DELETE request often has 204 http code with no content
        return;
    }

    /**
     * Update user (Returns an User)
     *
     * @Nelmio\ApiDoc(
     *  section="User",
     *  resource=true,
     *  requirements={
     *      {"name"="user_id", "description"="User ID"}
     *  },
     *  input={
     *      "class"="ExampleProject\Bundle\UserBundle\Entity\User",
     *      "groups"={"update"}
     *  },
     *  output={
     *      "class"="ExampleProject\Bundle\UserBundle\Entity\User",
     *      "groups"={"alwaysGet", "standardGet"}
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when validation error",
     *      401="Returned when authorization fails",
     *      404="Returned when resource not found"
     *  }
     * )
     *
     * @param Request $request
     * @param User $user
     * @Config\ParamConverter("name", converter="user")
     * @return User
     */
    public function updateAction(Request $request, User $user)
    {
        // Step 1: Authentication
        $this->auth->isGranted(UserVoter::UPDATE, $user);

        // Step 2: Deserialize the request data into object properties automatically
        $this->deserializer->deserialize($request, $user, 'update');

        // Step 3: Save the changes if everything is okay (no exception thrown until this point)
        $this->userRepository->save($user);

        // Step 4: Maybe log the changes
        $this->eventDispatcher->dispatch(CoreEvents::CHANGE_LOG, new ChangeLogEvent($user, $user));

        // Return updated user
        return $user;
    }
}
