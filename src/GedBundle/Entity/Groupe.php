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
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User",mappedBy="groupes")
     * @ORM\JoinTable(name="groupe_user",
     *     joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") },
     *     inverseJoinColumns={ @ORM\JoinColumn(name="groupe_id", referencedColumnName="id") }
     *               )
     */
    private $users;


    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $label;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GroupeFile", mappedBy="groupe")
     *
     */
    private $files;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUsers()
    {
        return $this->users;
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

    public function addFile(GroupeFile $file)
    {
        $this->files[] = $file;
    }

    public function removeFile(GroupeFile $file)
    {
        $this->files->removeElement($file);
    }

    public function getFiles()
    {
        $this->files;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    public function setFile($files)
    {
        $this->files = $files;
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->files = new ArrayCollection();
    }
}
