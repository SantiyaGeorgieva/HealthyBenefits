<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping as ORM_Mapping;
/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var int
     *
     * @ORM\Column(name="publicationId", type="integer")
     */
    private $publicationId;

    /**
     * @var Publication
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Publication", inversedBy="comments")
     * @ORM\JoinColumn(name="publicationId", referencedColumnName="id")
     */
    private $author_comment;

    public function __construct()
    {
        $this->dateAdded = new \DateTime("now");
    }

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user_comment;

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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Comment
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param integer $publicationId
     *
     * @return Comment
     */
    public function setPublicationId($publicationId)
    {
        $this->publicationId = $publicationId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * @param \AppBundle\Entity\Publication $author_comment
     *
     * @return $this
     */
    public function setAuthorComment(Publication $author_comment = null)
    {
        $this->author_comment = $author_comment;

        return $this;
    }

    /**
     * @param integer $userId
     * @return Comment
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param \AppBundle\Entity\User $user_comment
     * @return $this
     */
    public function setUserComment(User $user_comment = null)
    {
        $this->user_comment = $user_comment;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserComment()
    {
        return $this->user_comment;
    }
}

