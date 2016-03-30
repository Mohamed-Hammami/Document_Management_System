<?php

namespace GedBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\UserRepository")
 */
class User extends BaseUser
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
     *  @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groupes")
     * @ORM\JoinTable(name="groupe_user",
     *     joinColumns={ @ORM\JoinColumn(name="groupe_id", referencedColumnName="id") },
     *     inverseJoinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") }
     *              )
     */
    protected $groupes;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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

    public function __construct()
    {
        parent::__construct();
        $this->groupes = new ArrayCollection();
    }
}
