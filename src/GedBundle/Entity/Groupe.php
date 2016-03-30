<?php

namespace GedBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\GroupeRepository")
 */
class Groupe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection@
     *
     * @ORM\ManyToMany(targetEntity="User",mappedBy="groupes")
     * @ORM\JoinTable(name="groupe_user",
     *     joinColumns={ @ORM\JoinColumn(name="groupe_id", referencedColumnName="id") },
     *     inverseJoinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") }
     *               )
     */
    private $users;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    public function setUser(ArrayCollection $users)
    {
        $this->users = $users;
    }
    public function getUser()
    {
        return $this->users;
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
}
