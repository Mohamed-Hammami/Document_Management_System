<?php

namespace GedBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Folder
 *
 * @Gedmo\Tree(type="nested")
 *
 * @ORM\Table(name="folder")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\FolderRepository")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 *
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="createdBy"),
 *      @ORM\AssociationOverride(name="updatedBy")
 *      })
 *
 */
class Folder extends Node
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
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;


    /**
     * @var int
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @var Folder
     *
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Folder")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @var Folder
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     *
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     *
     * @ORM\OneToMany(targetEntity="File", mappedBy="folder")
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

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return Folder
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param Folder $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * @return Folder
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Folder $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function addFile(File $file)
    {
        $file->setFolder($this);
        $this->files[] = $file;
        return $this;
    }

    public function removeFile(File $file)
    {
        $file->setFolder(null);
        $this->files->removeElement($file);

    }
}
