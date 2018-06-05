<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
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
     * @var bool
     *
     * @ORM\Column(name="isReaded", type="boolean")
     */
    private $isReaded;

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text")
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text")
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var int
     *
     * @ORM\Column(name="senderId", type="integer")
     */
    private $senderId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(name="senderId", referencedColumnName="id")
     */
    private $senderUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="recipientId", type="integer")
     */
    private $recipientId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(name="recipientId", referencedColumnName="id")
     */
    private $recipientUserId;


    public function __construct()
    {
        $this->dateAdded = new \DateTime("now");
        $this->isReaded = false;
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
     * Set content
     *
     * @param string $content
     *
     * @return Message
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
     * @return Message
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
     * Set senderId
     *
     * @param integer $senderId
     *
     * @return Message
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * Get senderId
     *
     * @return int
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Set recipientId
     *
     * @param integer $recipientId
     *
     * @return Message
     */
    public function setRecipientId($recipientId)
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    /**
     * Get recipientId
     *
     * @return int
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @return User
     */
    public function getSenderUserId()
    {
        return $this->senderUserId;
    }

    /**
     *  @param \AppBundle\Entity\User $senderUserId
     *
     *  @return $this
     */
    public function setSenderUserId(User $senderUserId = null)
    {
        $this->senderUserId = $senderUserId;

        return $this;
    }

    /**
     * @return User
     */
    public function getRecipientUserId()
    {
        return $this->recipientUserId;
    }

    /**
     *  @param \AppBundle\Entity\User $recipientUserId
     *
     *  @return $this
     */
    public function setRecipientUserId(User $recipientUserId = null)
    {
        $this->recipientUserId = $recipientUserId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReaded()
    {
        return $this->isReaded;
    }

    /**
     * @param bool $isReaded
     */
    public function setIsReaded($isReaded)
    {
        $this->isReaded = $isReaded;
    }
}

