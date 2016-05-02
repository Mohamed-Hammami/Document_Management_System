<?php

namespace GedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

/**
 * Version
 *
 * @ORM\Table(name="version")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\VersionRepository")
 *
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="createdBy"),
 *      @ORM\AssociationOverride(name="updatedBy")
 * })
 *
 * @Vich\Uploadable
 */
class Version extends Node
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="isLast", type="boolean", nullable=true)
     */
    private $isLast;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="File", inversedBy="versions")
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="fileName", type="string")
     */
    private $fileName;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $size;


    /**
     *
     * @Vich\UploadableField(mapping="file_version", fileNameProperty="fileName")
     *
     */
    private $fileContent;

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
     * Set type
     *
     * @param string $type
     * @return Version
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isLast
     *
     * @param boolean $isLast
     * @return Version
     */
    public function setIsLast($isLast)
    {
        $this->isLast = $isLast;

        return $this;
    }

    /**
     * Get isLast
     *
     * @return boolean 
     */
    public function getIsLast()
    {
        return $this->isLast;
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
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return BaseFile
     */
    public function getFileContent()
    {
        return $this->fileContent;
    }

    /**
     * @param BaseFile $fileContent
     *
     * @return Version
     */
    public function setFileContent(BaseFile $fileContent = null)
    {
        $this->fileContent = $fileContent;

        if($fileContent){
            $this->setUpdated(new \DateTime('now'));
        }

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getFormattedSize()
    {

        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $this->size > 0 ? floor(log($this->size, 1024)) : 0;
        return number_format($this->size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
