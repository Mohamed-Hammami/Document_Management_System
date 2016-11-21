<?php

namespace GedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="GedBundle\Repository\ConfigRepository")
 */
class Config
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
     * @var int
     *
     * @ORM\Column(name="rootId", type="integer")
     */
    private $rootId;


    /**
     * @var bool
     *
     * @ORM\Column(name="firstTime", type="boolean")
     */
    private $firstTime;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="skin", type="string", length=255)
     */
    private $skin;


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
     * @return boolean
     */
    public function isFirstTime()
    {
        return $this->firstTime;
    }

    /**
     * @param boolean $firstTime
     */
    public function setFirstTime($firstTime)
    {
        $this->firstTime = $firstTime;
    }



    /**
     * Set language
     *
     * @param string $language
     * @return Config
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set skin
     *
     * @param string $skin
     * @return Config
     */
    public function setSkin($skin)
    {
        $this->skin = $skin;

        return $this;
    }

    /**
     * Get skin
     *
     * @return string 
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * @return mixed
     */
    public function getRootId()
    {
        return $this->rootId;
    }

    /**
     * @param mixed $rootId
     */
    public function setRootId($rootId)
    {
        $this->rootId = $rootId;
    }



    public function __construct()
    {
        $this->setFirstTime(true);

    }
}
