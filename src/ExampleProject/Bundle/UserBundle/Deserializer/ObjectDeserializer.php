<?php

namespace ExampleProject\Bundle\CoreBundle\Serializer;

use ExampleProject\Bundle\UserBundle\Exception\ValidationException;
use Exception;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use ExampleProject\Bundle\CoreBundle\Entity\IdInterface;
use ExampleProject\Bundle\CoreBundle\Exception\CoreException;
use ExampleProject\Bundle\CoreBundle\Exception\DeserializationException;
use ExampleProject\Bundle\CoreBundle\Handler\ValidationErrorsHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ObjectDeserializer uses JMS serializer to deserialize the data received from request body.
 *
 * Right after the data is mapped into an object, it uses validator to validate the object, and then handles
 * the validation errors.
 *
 * Object deserializer dispatches some simple events, which serve as extension points (Open for extension principle).
 */
class ObjectDeserializer implements ObjectDeserializerInterface
{
    /**
     * Dependency inversion principle: depend upon abstractions not upon concrete classes
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var SerializerGroupsInterface
     */
    private $serializerGroups;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidationErrorsHandlerInterface
     */
    private $validationErrorsHandler;

    /**
     * An array consisting of callbacks to be executed at given events
     *
     * @var array
     */
    private $callbacks;

    /**
     * @param SerializerInterface $serializer
     * @param SerializerGroupsInterface $serializerGroups
     * @param ValidatorInterface $validator
     * @param ValidationErrorsHandlerInterface $validationErrorsHandler
     */
    public function __construct(
        SerializerInterface $serializer,
        SerializerGroupsInterface $serializerGroups,
        ValidatorInterface $validator,
        ValidationErrorsHandlerInterface $validationErrorsHandler
    ) {
        $this->serializer = $serializer;
        $this->serializerGroups = $serializerGroups;
        $this->validator = $validator;
        $this->validationErrorsHandler = $validationErrorsHandler;
    }

    /**
     * The returned object is the newly created or the updated one.
     *
     * {@inheritdoc}
     */
    public function deserialize(Request $request, $object, $groups = Constraint::DEFAULT_GROUP, array $callbacks = array())
    {
        $this->callbacks = $callbacks;
        $className = is_object($object) ? get_class($object) : $object;
        $groups = (array) $groups;

        $this->dispatch(DeserializerEvents::PRE_REFERENCE, $object, $request);
        $this->setReferences($object);

        try {
            $context = new DeserializationContext();
            $context->setGroups($groups);

            $this->dispatch(DeserializerEvents::PRE_DESERIALIZE, $object, $request);
            $content = $request->getContent();
            $deserializedObject = $this->serializer->deserialize(
                $content,
                $className,
                $request->getRequestFormat(),
                $context
            );
            $this->dispatch(DeserializerEvents::POST_DESERIALIZE, $object, $request);
        } catch (Exception $e) {
            throw new ValidationException($e->getMessage());
        }

        $this->dispatch(DeserializerEvents::PRE_VALIDATION, $object, $request);

        $errors = $this->validator->validate($deserializedObject, null, $groups);
        $this->validationErrorsHandler->handle($errors);

        $this->dispatch(DeserializerEvents::POST_VALIDATION, $object, $request);

        // Adds the groups to serializer groups, so the same data which is accepted, is also returned
        foreach ($groups as $group) {
            $this->serializerGroups->add($group); // We get all the data back, which might have been affected
        }

        return $deserializedObject;
    }

    /**
     * This method is not declared in the interface!
     *
     * TODO if something is to be done I like to write a statement like this...
     *
     * We would have to depend upon this concrete implementation somewhere to be able to use it.
     *
     * Even if it was and we would implement it differently (return a different type or throw an unexpected exception), that would break LSP. This line is too long...
     *
     * @return ObjectDeserializerInterface
     */
    public function doSomethingSpecial()
    {
        return $this;
    }

    /**
     * Function is only used internally, this is why it is private, closed for modification
     * @param string $dispatchedEvent
     * @param object $object
     * @param Request $request
     */
    private function dispatch($dispatchedEvent, $object, Request $request)
    {
        foreach ($this->callbacks as $event => $callback) {
            if ($dispatchedEvent === $event) {
                $callback($object, $request);
            }
        }
    }
}
