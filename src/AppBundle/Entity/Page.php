<?php
/**
 * Created by PhpStorm.
 * User: Linking
 * Date: 17/02/2016
 * Time: 20:27
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Page
 * @ORM\Entity
 * @ORM\Table(name="page")
 */

class Page
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $subtitle;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $isFullscreen;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="text")
     */
    protected $slug;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $randomHeaderColors;



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
     * Set name
     *
     * @param string $name
     *
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     *
     * @return Page
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set isFullscreen
     *
     * @param boolean $isFullscreen
     *
     * @return Page
     */
    public function setIsFullscreen($isFullscreen)
    {
        $this->isFullscreen = $isFullscreen;

        return $this;
    }

    /**
     * Get isFullscreen
     *
     * @return boolean
     */
    public function getIsFullscreen()
    {
        return $this->isFullscreen;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set randomHeaderColors
     *
     * @param boolean $randomHeaderColors
     *
     * @return Page
     */
    public function setRandomHeaderColors($randomHeaderColors)
    {
        $this->randomHeaderColors = $randomHeaderColors;

        return $this;
    }

    /**
     * Get randomHeaderColors
     *
     * @return boolean
     */
    public function getRandomHeaderColors()
    {
        return $this->randomHeaderColors;
    }
}
