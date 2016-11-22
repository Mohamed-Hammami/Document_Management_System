<?php

namespace GedBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Workspace
 *
 * @ORM\Table(name="workspace")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\WorkspaceRepository")
 */
class Workspace
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
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="WorkspaceFile", mappedBy="file")
     *
     */
    private $files;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="WorkspaceFolder", mappedBy="folder")
     *
     */
    private $folders;


    /**
     *
     * @var User
     *
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="workspace", cascade={"persist"})
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $memo;


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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param ArrayCollection $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return ArrayCollection
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * @param ArrayCollection $folders
     */
    public function setFolders($folders)
    {
        $this->folders = $folders;
    }

    public function addFile(WorkspaceFile $file)
    {
        $this->files[] = $file;
    }

    public function removeFile(WorkspaceFile $file)
    {
        $this->files->removeElement($file);
    }

    public function addFolder($folder)
    {
        $this->folders[] = $folder;
    }

    public function removeFolder($folder)
    {
        $this->folders->removeElement($folder);
    }

    /**
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * @param string $memo
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;
    }



    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->folders = new ArrayCollection();

    }

}
