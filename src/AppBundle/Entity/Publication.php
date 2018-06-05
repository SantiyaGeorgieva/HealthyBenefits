<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublicationRepository")
 */
class Publication
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="picture_url", type="text", nullable=true)
//     */
//    private $pictureUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;


    /**
     * @var string
     *
     * @ORM\Column(name="criteria", type="text")
     */
    private $criteria;

    /**
     * @var string
     *
     * @ORM\Column(name="criteria_food", type="text", nullable=true)
     */
    private $criteria_food;

    /**
     * @var integer
     *
     *@ORM\Column(name="view_count", type="integer")
     */
    private $viewCount;

    /**
     * @var integer
     *
     *@ORM\Column(name="view_likes", type="integer")
     */
    private $viewLikes;

    /*
     * @var string
     */
    private $summary;

    /**
     * @var int
     *
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="publications")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $author;

    public function __construct()
    {
        $this->dateAdded = new \DateTime("now");
        $this->viewCount = 0;
        $this->viewLikes = 0;
    }

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="author_comment")
     */
    private $comments;



    public function __constructComments()
    {
        $this->comments = new ArrayCollection();
    }
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
     * Set title
     *
     * @param string $title
     *
     * @return Publication
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Publication
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
     * @return Publication
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
     * @return string
     */
    public function getDate()
    {
        return $this->dateAdded;
    }

    /**
     * @param string
     */
    public function setSummary()
    {
        $this->summary = substr($this->getContent(), 0, strlen($this->getContent()) / 3) . "...";
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        if ($this->summary === null) {
            $this->setSummary();
        }

        return $this->summary;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param integer $authorId
     *
     * @return Publication
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \AppBundle\Entity\User $author
     * @return $this
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Publication
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

//    /**
//     * @return string
//     */
//    public function getPictureUrl()
//    {
//        return $this->pictureUrl;
//    }
//
//    /**
//     * @param string $pictureUrl
//     */
//    public function setPictureUrl($pictureUrl)
//    {
//        $this->pictureUrl = $pictureUrl;
//    }

    /**
     * @return string
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param string $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @return string
     */
    public function getCriteriaFood()
    {
        return $this->criteria_food;
    }

    /**
     * @param string $criteria_food
     */
    public function setCriteriaFood($criteria_food)
    {
        $this->criteria_food = $criteria_food;
    }

    function _toString()
    {
        return $this->dateAdded;
    }

    /**
     * @return int
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param int $viewCount
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return int
     */
    public function getViewLikes()
    {
        return $this->viewLikes;
    }

    /**
     * @param int $viewLikes
     */
    public function setViewLikes($viewLikes)
    {
        $this->viewLikes = $viewLikes;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}

