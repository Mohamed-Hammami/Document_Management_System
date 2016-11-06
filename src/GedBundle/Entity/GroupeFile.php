<?php

namespace GedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupeFile
 *
 * @ORM\Table(name="groupe_file")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\GroupeFileRepository")
 */
class GroupeFile
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
     * @var int
     *
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;


    /**
     *
     * @ORM\ManyToOne(targetEntity="File", inversedBy="groupes")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $file;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Groupe", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $groupe;

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
     * Set level
     *
     * @param integer $level
     * @return GroupeFile
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
        $file->addGroupe($this);
    }

    /**
     * @return Groupe
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * @param Groupe $groupe
     */
    public function setGroupe($groupe)
    {
        $this->groupe = $groupe;
        $groupe->addFile($this);
    }



}
