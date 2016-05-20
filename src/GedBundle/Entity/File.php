<?php

namespace GedBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\FileRepository")
 *
 *@ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="createdBy"),
 *      @ORM\AssociationOverride(name="updatedBy")
 * })
 *
 */
class File extends Node
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
     * @var Folder
     *
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="files")
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Version", mappedBy="file", cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $versions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="file", cascade={"persist", "remove"})
     * @ORM\JoinColumn()referencedColumnName="id", onDelete="CASCADE")
     */
    private $comments;

    /**
     * @var Nature
     *
     * @ORM\ManyToOne(targetEntity="Nature", inversedBy="files")
     */
    private $nature;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiration;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="files")
     */
    protected $tags;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    public function getFolder()
    {
        return $this->folder;
    }


    public function setFolder($folder)
    {
        $this->folder = $folder;
    }



    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function addComment(Comment $comment)
    {
        $comment->setFile($this);
        $this->comments->add($comment);

        return $this;
    }

    public function removeComment(Comment $comment)
    {
        $comment->setFile(null);
        $this->comments->removeElement($comment);
    }

    public function addVersion(Version $version)
    {
        $version->setFile($this);
        $this->versions->add($version);

        return $this;
    }

    public function removeVersion(Version $version)
    {
        $version->setFile(null);
        $this->versions->removeElement($version);

    }

    /**
     * @return ArrayCollection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param ArrayCollection $versions
     */
    public function setVersions($versions)
    {
        $this->versions = $versions;
    }


    /**
     * @return Nature
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * @param Nature $nature
     */
    public function setNature(Nature $nature)
    {
        $nature->addFile($this);
        $this->nature = $nature;
    }

    /**
     * @param Nature $nature
     */
    public function removeNature(Nature $nature)
    {
        $nature->removeFile($this);
        $this->setNature(null);
    }

    /**
     * @return \DateTime
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param \DateTime $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }


    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function addTag(Tag $tag)
    {
        $tag->addFile($this);
        $this->tags->add($tag);

        return $this;
    }

    public function removeTag(Tag $tag)
    {
        $tag->removeFile($this);
        $this->tags->removeElement($tag);
    }

    public function __toString()
    {
        return $this->getName();
    }


    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->versions = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


}
