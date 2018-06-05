<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;


    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="pictureUrl", type="text", nullable=true)
//     */
//    private $pictureUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Publication", mappedBy="author")
     */
    private $publications;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     */
    private $roles;

    public function __construct()
    {
//        $this->setPictureUrl(null);
        $this->setInfo(null);
        $this->publications = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->comments = new ArrayCollection();
//        $this->aboutEdit = new ArrayCollection();
    }

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Comment", mappedBy="user_comment")
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\PublicationLike", mappedBy="userLike")
     */
    private $publicationLikes;

//    /**
////     * @var ArrayCollection
////     *
////     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AboutUs", mappedBy="adminEdit")
////     */
////    private $aboutEdit;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublications()
    {
        return $this->publications;
    }

    /**
     * @param \AppBundle\Entity\Publication $publication
     *
     * @return User
     */
    public function addPost($publication) {
        $this->publications[] = $publication;

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
     * @return User
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

//    /**
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getAboutEdit()
//    {
//        return $this->aboutEdit;
//    }
//
//    /**
//     * @param \AppBundle\Entity\AboutUs $aboutEdit
//     *
//     * @return User
//     */
//    public function setAboutEdit(AboutUs $aboutEdit)
//    {
//        $this->aboutEdit[] = $aboutEdit;
//
//        return $this;
//    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role)
        {
            /** @var $role Role */
            $stringRoles[] = $role->getRole();
        }
        return $stringRoles;
    }

    /**
     * Remove userRoles
     *
     * @param Role $userRoles
     */
    public function removeUserRole($userRoles)
    {
        $this->roles->removeElement($userRoles);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
    * @param Publication $publication
    * @return bool
    */
    public function isAuthor($publication)
    {
        return $publication->getAuthorId() == $this->getId();
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function isAuthorComment($comment)
    {
        return $comment->getUserComment()->getUsername() == $this->getUsername();
    }

    //    function that check the current user is admin and if it --> returns true
    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    //    function that check the current user is superadmin and if it --> returns true
    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return in_array("ROLE_SUPERADMIN", $this->getRoles());
    }

    function __toString()
    {
        return $this->fullName;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublicationLikes()
    {
        return $this->publicationLikes;
    }

    /**
     * @param \AppBundle\Entity\User $publicationLikes
     *
     * @return User
     */
    public function setPublicationLikes($publicationLikes)
    {
        $this->publicationLikes[] = $publicationLikes;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
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


