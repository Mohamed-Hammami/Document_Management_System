<?php

namespace GedBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Avanzu\AdminThemeBundle\Model\UserInterface as ThemeUser;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\UserRepository")

 */
class User extends BaseUser implements ThemeUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $surName;


    /**
     *  @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Groupe", inversedBy="users")
     * @ORM\JoinTable(name="groupe_user",
     *     joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") },
     *     inverseJoinColumns={ @ORM\JoinColumn(name="groupe_id", referencedColumnName="id") }
     *              )
     */
    protected $groupes;

    /**
     * @var Departement
     *
     * @ORM\ManyToOne(targetEntity="Departement")
     */
    private $departement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $avatar;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    protected $registrationDate;

    /**
     * @var Workspace
     *
     * @ORM\OneToOne(targetEntity="Workspace", mappedBy="user")
     */
    protected $workspace;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSurName()
    {
        return $this->surName;
    }

    /**
     * @param mixed $surName
     */
    public function setSurName($surName)
    {
        $this->surName = $surName;
    }

    /**
     * @return Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * @param Departement $departement
     */
    public function setDepartement($departement)
    {
        $this->departement = $departement;
    }

    public function addGroupes(Groupe $groupe)
    {
        $groupe->addUser($this);
        $this->groupes->add($groupe);
        return $this;
    }

    public function removeGroupes(Groupe $groupe)
    {
        $groupe->removeUser($this);
        $this->groupes->removeElement($groupe);
        return $this;
    }

    public function getGroupes()
    {
        return $this->groupes;
    }


    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @param mixed $registrationDate
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;
    }


    public function __construct()
    {
        parent::__construct();
        $this->groupes = new ArrayCollection();
        $this->updatedAt =  new \DateTime('now');
    }

//   userInterface functions

    public function getUsername()
    {
        return parent::getUsername();
    }

    public function getMemberSince()
    {
        $this->getRegistrationDate();
    }

    public function isOnline(){}

    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * @return Workspace
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @param Workspace $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
    }



}

