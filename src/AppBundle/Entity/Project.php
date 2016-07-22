<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="image_url", type="string", length=255)
     */
    private $imageUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var array
     *
     * @ORM\Column(name="project_images", type="simple_array", nullable=true)
     */
    private $projectImages;

    /**
     * @var array
     *
     * @ORM\Column(name="project_images_description", type="simple_array", nullable=true)
     */
    private $projectImagesDescription;

    /**
     * @ORM\Column(name="languages", type="simple_array", nullable=true)
     */
    private $languages;

    /**
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ProjectPlatform", inversedBy="projects")
     */
    private $platforms;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ProjectCategory", inversedBy="projects")
     */
    private $categories;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Project
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set projectImages
     *
     * @param array $projectImages
     *
     * @return Project
     */
    public function setProjectImages($projectImages)
    {
        $this->projectImages = ($projectImages != null) ? array_filter($projectImages) : null;

        return $this;
    }

    /**
     * Get projectImages
     *
     * @return array
     */
    public function getProjectImages()
    {
        return ($this->projectImages != null) ? array_filter($this->projectImages) : null;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\ProjectCategory $category
     *
     * @return Project
     */
    public function addCategory(\AppBundle\Entity\ProjectCategory $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\ProjectCategory $category
     */
    public function removeCategory(\AppBundle\Entity\ProjectCategory $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return ProjectCategory[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
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
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return Project
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Project
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
     * @return Project
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
     * Set projectImagesDescription
     *
     * @param array $projectImagesDescription
     *
     * @return Project
     */
    public function setProjectImagesDescription($projectImagesDescription)
    {
        $this->projectImagesDescription = $projectImagesDescription;

        return $this;
    }

    /**
     * Get projectImagesDescription
     *
     * @return array
     */
    public function getProjectImagesDescription()
    {
        return $this->projectImagesDescription;
    }

    /**
     * @return \stdClass[]
     */
    public function getImagesObject(){
        $images = [];
        for($i = 0; $i < count($this->getProjectImages()); $i++){
            $image = new \stdClass();
            $image->url = $this->projectImages[$i];
            $image->description = $this->getProjectImagesDescription()[$i];
            $images[$i] = $image;
        }
        return $images;
    }

    /**
     * Set platforms
     *
     * @param array $platforms
     *
     * @return Project
     */
    public function setPlatforms($platforms)
    {
        $this->platforms = $platforms;

        return $this;
    }

    /**
     * Get platforms
     *
     * @return array
     */
    public function getPlatforms()
    {
        return $this->platforms;
    }

    /**
     * Add platform
     *
     * @param \AppBundle\Entity\ProjectPlatform $platform
     *
     * @return Project
     */
    public function addPlatform(\AppBundle\Entity\ProjectPlatform $platform)
    {
        $this->platforms[] = $platform;

        return $this;
    }

    /**
     * Remove platform
     *
     * @param \AppBundle\Entity\ProjectPlatform $platform
     */
    public function removePlatform(\AppBundle\Entity\ProjectPlatform $platform)
    {
        $this->platforms->removeElement($platform);
    }

    /**
     * Set languages
     *
     * @param array $languages
     *
     * @return Project
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }
}
