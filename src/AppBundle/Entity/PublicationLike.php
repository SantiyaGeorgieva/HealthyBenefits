<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicationLike
 *
 * @ORM\Table(name="publication_like")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublicationLikeRepository")
 */
class PublicationLike
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
     * @ORM\Column(name="publicationId", type="integer")
     */
    private $publicationId;

    /**
     * @var Publication
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Publication", inversedBy="publicationLikes")
     * @ORM\JoinColumn(name="publicationId", referencedColumnName="id")
     */
    private $publicationLike;

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer", unique=false)
     */
    private $userId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="publicationLikes")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $userLike;

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
     * Set publicationId
     *
     * @param integer $publicationId
     *
     * @return PublicationLike
     */
    public function setPublicationId($publicationId)
    {
        $this->publicationId = $publicationId;

        return $this;
    }

    /**
     * Get publicationId
     *
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return PublicationLike
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return Publication
     */
    public function getPublicationLike()
    {
        return $this->publicationLike;
    }

    /**
     *  @param \AppBundle\Entity\Publication $publicationLike
     *
     *  @return $this
     */
    public function setPublicationLike(Publication $publicationLike = null)
    {
        $this->publicationLike = $publicationLike;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserLike()
    {
        return $this->userLike;
    }

    /**
     *  @param \AppBundle\Entity\User $userLike
     *
     *  @return $this
     */
    public function setUserLike(User $userLike = null)
    {
        $this->userLike = $userLike;

        return $this;
    }
}

