<?php

namespace ExampleProject\Bundle\CoreBundle\Serializer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

interface ObjectDeserializerInterface
{
    /**
     * Deserialize the entity from request into an object
     *
     * Documentation is very important!
     *
     * @param Request $request
     * @param $object - If existing, the object itself.
     *                  If unexisting, then FQN (fully qualified name) 'MyNamespace\MyObject', so a new object of that class is created.
     * @param $groups - Examples: 'registration', 'update'... should be string or array
     * @param array $callbacks Functions which are executed at defined events.
     *
     * Example usage:
     *
     * $this->deserialize(
     *      $request,
     *      new User(),
     *      'registration',
     *      array(
     *          DeserializerEvents::POST_VALIDATION => function(User $user) {
     *              $user->setSomethingUponValidRegistration();
     *          }
     *      );
     * )
     * @return mixed  - (The object with the deserialized data)
     */
    public function deserialize(Request $request, $object, $groups = Constraint::DEFAULT_GROUP, array $callbacks = array());
}