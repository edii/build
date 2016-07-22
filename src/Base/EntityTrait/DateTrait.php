<?php

namespace Base\EntityTrait;

/**
 * Trait for createAt and updateAt fields in Entities
 */
trait DateTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="updated_at")
     */
    private $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function preCreateChangeDate()
    {
        $this->createdAt = $this->createdAt ?: new \DateTime();
        $this->updatedAt = $this->updatedAt ?: new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdateChangeDate()
    {
        $this->updatedAt = new \DateTime();
    }
}
