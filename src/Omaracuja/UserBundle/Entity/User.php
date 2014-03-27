<?php
namespace Omaracuja\UserBundle\Entity;


use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_user")
 */
class User extends BaseUser
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function hasRole($role) {
    if(in_array($role, $this->getRoles())){
        return true;
    }
    return false;
    }
    
    public function isAdmin() {
        return $this->hasRole('ADMIN');
    }
    
    
}
