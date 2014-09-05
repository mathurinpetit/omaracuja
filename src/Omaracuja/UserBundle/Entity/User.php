<?php

namespace Omaracuja\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use Omaracuja\FrontBundle\Entity\BlogPost as BlogPost;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_user")
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $actif = false;

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
        return $this->actif;
    }

    public function activate() {
        $this->actif = true;
    }

    public function desactivate() {
        $this->actif = false;
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

}
