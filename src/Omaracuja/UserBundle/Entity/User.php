<?php

namespace Omaracuja\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToOne(targetEntity="Avatar")
     */
    protected $selectedAvatar;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    
    public function setSelectedAvatar($avatar) {
        $this->selectedAvatar = $avatar;
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
        if(!count($this->avatars)){
            return "/data/avatars/omaracuja_avatar.jpg";
        }
        return $this->selectedAvatar->getWebPath();
    }

}
