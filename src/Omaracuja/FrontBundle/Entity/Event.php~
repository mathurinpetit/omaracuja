<?php
namespace Omaracuja\FrontBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Omaracuja\UserBundle\Entity\User as User;

use \DateTime;
/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_event")
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\EventRepository")
 */
class Event
{
    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=1024, nullable=false)
     */
    private $title;
    
    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;
    
    /**
     * @var boolean $public
     *
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    private $public;

     /**
     * @var datetime $startAt
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     */
    private $startAt;
    
    /**
     * @var datetime $endAt
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=false)
     */
    private $endAt;
    
    /**
     * @var string $place
     *
     * @ORM\Column(name="place", type="string", length=1024, nullable=false)
     */
    private $place;
    
   /**
    * @ORM\ManyToMany(targetEntity="Omaracuja\UserBundle\Entity\User", inversedBy="proposedEvents")
    * @ORM\JoinTable(name="omaracuja_user_proposed_event")
    */
    private $proposedTeam;
    
   /**
    * @ORM\ManyToMany(targetEntity="Omaracuja\UserBundle\Entity\User", inversedBy="participateEvents")
    * @ORM\JoinTable(name="omaracuja_user_participate_event")
    */
    private $actualTeam;
    
    

    public function __construct() {
        $this->createdAt = new DateTime();
        $this->public = true;
        $this->proposedTeam = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actualTeam = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}
