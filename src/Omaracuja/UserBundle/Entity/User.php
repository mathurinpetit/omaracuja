<?php

namespace Omaracuja\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use Omaracuja\FrontBundle\Entity\BlogPost as BlogPost;
use Omaracuja\FrontBundle\Entity\Event as Event;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_user")
 * @ORM\Entity(repositoryClass="Omaracuja\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToMany(targetEntity="Avatar", mappedBy="user")
     */
    protected $avatars;
    
     /**
     * @ORM\OneToMany(targetEntity="Omaracuja\FrontBundle\Entity\BlogPost", mappedBy="author")
     */
    protected $blogPosts;

    /**
     * @ORM\OneToOne(targetEntity="Avatar")
     */
    protected $selectedAvatar;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="firstname", type="string", length=100, nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\ManyToMany(targetEntity="Omaracuja\FrontBundle\Entity\Event", mappedBy="proposedTeam")
     */
    protected $proposedEvents;
    
    /**
     * @ORM\ManyToMany(targetEntity="Omaracuja\FrontBundle\Entity\Event", mappedBy="actualTeam")
     */
    protected $participateEvents;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="Omaracuja\FrontBundle\Entity\Event", mappedBy="refusedUsers")
     */
    protected $refusedEvents;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function getSelectedAvatar() {
        return $this->selectedAvatar;
    }

    public function setSelectedAvatar($avatar) {
        $this->selectedAvatar = $avatar;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    public function hasRole($role) {
        if (in_array($role, $this->getRoles())) {
            return true;
        }
        return false;
    }

    public function isAdmin() {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function isActif() {
        return $this->enabled;
    }

    public function activate() {
        $this->enabled = true;
    }

    public function desactivate() {
        $this->enabled = false;
    }

    public function getCurrentAvatarPath() {
        if (!count($this->avatars)) {
            return "/data/avatars/omaracuja_avatar.jpg";
        }
        if (!$this->selectedAvatar) {
            return "/data/avatars/omaracuja_avatar.jpg";
        }
        return $this->selectedAvatar->getWebPath();
    }

    public function getAvatars() {
        return $this->avatars;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->avatars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->blogPosts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proposedEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->participateEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enabled = false;
        parent::__construct();
    }

    /**
     * Add avatars
     *
     * @param \Omaracuja\UserBundle\Entity\Avatar $avatars
     * @return User
     */
    public function addAvatar(\Omaracuja\UserBundle\Entity\Avatar $avatars)
    {
        $this->avatars[] = $avatars;

        return $this;
    }

    /**
     * Remove avatars
     *
     * @param \Omaracuja\UserBundle\Entity\Avatar $avatars
     */
    public function removeAvatar(\Omaracuja\UserBundle\Entity\Avatar $avatars)
    {
        $this->avatars->removeElement($avatars);
    }

    /**
     * Add blogPosts
     *
     * @param \Omaracuja\FrontBundle\Entity\BlogPost $blogPosts
     * @return User
     */
    public function addBlogPost(\Omaracuja\FrontBundle\Entity\BlogPost $blogPosts)
    {
        $this->blogPosts[] = $blogPosts;

        return $this;
    }

    /**
     * Remove blogPosts
     *
     * @param \Omaracuja\FrontBundle\Entity\BlogPost $blogPosts
     */
    public function removeBlogPost(\Omaracuja\FrontBundle\Entity\BlogPost $blogPosts)
    {
        $this->blogPosts->removeElement($blogPosts);
    }

    /**
     * Get blogPosts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlogPosts()
    {
        return $this->blogPosts;
    }

    /**
     * Add proposedEvents
     *
     * @param \Omaracuja\FrontBundle\Entity\Event $proposedEvents
     * @return User
     */
    public function addProposedEvent(\Omaracuja\FrontBundle\Entity\Event $proposedEvents)
    {
        $this->proposedEvents[] = $proposedEvents;

        return $this;
    }

    /**
     * Remove proposedEvents
     *
     * @param \Omaracuja\FrontBundle\Entity\Event $proposedEvents
     */
    public function removeProposedEvent(\Omaracuja\FrontBundle\Entity\Event $proposedEvents)
    {
        $this->proposedEvents->removeElement($proposedEvents);
    }

    /**
     * Get proposedEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProposedEvents()
    {
        return $this->proposedEvents;
    }

    /**
     * Get refusedEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRefusedEvents()
    {
        return $this->refusedEvents;
    }
    

    /**
     * Add participateEvents
     *
     * @param \Omaracuja\FrontBundle\Entity\Event $participateEvents
     * @return User
     */
    public function addParticipateEvent(\Omaracuja\FrontBundle\Entity\Event $participateEvents)
    {
        $this->participateEvents[] = $participateEvents;

        return $this;
    }

    /**
     * Remove participateEvents
     *
     * @param \Omaracuja\FrontBundle\Entity\Event $participateEvents
     */
    public function removeParticipateEvent(\Omaracuja\FrontBundle\Entity\Event $participateEvents)
    {
        $this->participateEvents->removeElement($participateEvents);
    }

    /**
     * Get participateEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipateEvents()
    {
        return $this->participateEvents;
    }
}
