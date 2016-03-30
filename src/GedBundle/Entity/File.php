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
     * @ORM\OneToMany(targetEntity="Version", mappedBy="file")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="Cascade")
     */
    private $versions;

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
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder)
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

    public function __construct()
    {
        $this->versions = new ArrayCollection();
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



}
