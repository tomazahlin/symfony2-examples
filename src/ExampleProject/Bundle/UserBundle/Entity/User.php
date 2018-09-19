<?php

namespace ExampleProject\Bundle\UserBundle\Entity;

// Explicit dependencies
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(
 *  name="user",
 *  indexes={
 *      @ORM\Index(name="name", columns={"name"}),
 *      @ORM\Index(name="email", columns={"email"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="ExampleProject\Bundle\UserBundle\Repository\UserRepository")
 *
 * @JMS\ExclusionPolicy("none")
 * @JMS\AccessType("public_method")
 *
 */
class User implements IdInterface, OwnerAwareInterface
{
    use SoftDeleteableDateTimeTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\AccessType("reflection")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @JMS\Groups({"registration", "update"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @JMS\Groups({"registration", "update"})
     */
    private $email;

    /**
     * @var Settings
     *
     * @ORM\OneToOne(targetEntity="Settings", mappedBy="user", cascade={"persist"})
     */
    private $settings;

    /**
     * Constructor.
     *
     * Very important is the public "api" of the class' methods and their return values.
     *
     * @param $username
     * @param $email
     *
     * Value objects (such as this User object) should never be in an invalid state.
     *
     * For example when a new instance is created, all the required data should be set to the properties.
     * This way we avoid having an invalid state - User without Profile, without username...
     */
    public function __construct($username, $email)
    {
        $this->username = $username;
        $this->email = $email;

        // Sometimes I like to use blank lines to promote readability
        // There are no inline dependencies, but instead they are declared on top

        // Avoid dynamically declared properties
        $this->created = new DateTime();

        $this->settings = new Settings();

        // Implicit dependency on OtherObject's method
        $this->settings->getSomeOtherObject()->setSomethingThere();

    }

    /**
     * Get ID
     *
     * Note, there is not setId() method implemented, because ID should not be changed by programmer.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this;
    }

    // Public methods are usually on the top, followed by protected and private methods
}
