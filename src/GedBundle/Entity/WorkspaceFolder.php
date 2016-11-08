<?php

namespace GedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkspaceFolder
 *
 * @ORM\Table(name="workspace_folder")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\WorkspaceFolderRepository")
 */
class WorkspaceFolder
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
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="folders")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $workspace;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="workspaces")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $folder;


    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="smallint", nullable=true)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="notification", type="smallint", nullable=true)
     */
    private $notification;



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
     * @return int
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param int $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        $folder->addWorkspace($this);
    }

    /**
     * @return mixed
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
        $workspace->addFolder($this);
    }


}
