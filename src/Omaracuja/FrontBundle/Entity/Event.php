<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Omaracuja\UserBundle\Entity\User as User;
use Omaracuja\FrontBundle\Entity\EventPicture as EventPicture;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_event")
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\EventRepository")
 */
class Event {

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
     * @ORM\Column(name="title", type="string", length=1024, nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var text $description     
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var datetime $createdAt     
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *       
     */
    private $createdAt;

    /**
     * @var boolean $public
     *
     * @ORM\Column(name="public", type="boolean", nullable=true)
     */
    private $public;

    /**
     * @var datetime $startAt    
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     * @Assert\NotBlank()
     */
    private $startAt;

    /**
     * @var datetime $endAt
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var string $place
     * 
     * @ORM\Column(name="place", type="string", length=1024, nullable=true)
     * @Assert\NotBlank()
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

    /**
     * @var float $mapX
     *
     * @ORM\Column(name="map_x", type="float", nullable=true)
     */
    private $mapX;

    /**
     * @var float $mapY
     *
     * @ORM\Column(name="map_y", type="float", nullable=true)
     */
    private $mapY;

    /**
     * @ORM\OneToOne(targetEntity="Omaracuja\FrontBundle\Entity\EventPicture", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(name="event_picture", referencedColumnName="id")
     */
    private $eventPicture;

    public function __construct() {
        $this->createdAt = new DateTime();
        $this->public = true;
        $this->startAt = new DateTime();
        $this->proposedTeam = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actualTeam = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title) {

        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Event
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Event
     */
    public function setPublic($public) {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic() {
        return $this->public;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return Event
     */
    public function setStartAt($startAt) {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt() {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Event
     */
    public function setEndAt($endAt) {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt() {
        return $this->endAt;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return Event
     */
    public function setPlace($place) {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace() {
        return $this->place;
    }

    /**
     * Add proposedTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $proposedTeam
     * @return Event
     */
    public function addProposedTeam(\Omaracuja\UserBundle\Entity\User $proposedTeam) {
        $this->proposedTeam[] = $proposedTeam;

        return $this;
    }

    /**
     * Remove proposedTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $proposedTeam
     */
    public function removeProposedTeam(\Omaracuja\UserBundle\Entity\User $proposedTeam) {
        $this->proposedTeam->removeElement($proposedTeam);
    }

    /**
     * Get proposedTeam
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProposedTeam() {
        return $this->proposedTeam;
    }

    /**
     * Add actualTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $actualTeam
     * @return Event
     */
    public function addActualTeam(\Omaracuja\UserBundle\Entity\User $actualTeam) {
        $this->actualTeam[] = $actualTeam;

        return $this;
    }

    /**
     * Remove actualTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $actualTeam
     */
    public function removeActualTeam(\Omaracuja\UserBundle\Entity\User $actualTeam) {
        $this->actualTeam->removeElement($actualTeam);
    }

    /**
     * Get actualTeam
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActualTeam() {
        return $this->actualTeam;
    }

    /**
     * Set mapX
     *
     * @param float $mapX
     * @return Event
     */
    public function setMapX($mapX) {
        $this->mapX = $mapX;

        return $this;
    }

    /**
     * Get mapX
     *
     * @return float 
     */
    public function getMapX() {
        return $this->mapX;
    }

    /**
     * Set mapY
     *
     * @param float $mapY
     * @return Event
     */
    public function setMapY($mapY) {
        $this->mapY = $mapY;

        return $this;
    }

    /**
     * Get mapY
     *
     * @return float 
     */
    public function getMapY() {
        return $this->mapY;
    }

    public function setPicture($ev) {
        $this->eventPicture = $ev; 
        return $this;
    }
    
    public function getPicture() {
        return $this->eventPicture;
    }
    
    public function getPicturePath() {
        if (!$this->eventPicture) {
            return "/data/events/omaracuja_event_no_picture.jpg";
        }
        return $this->getWebPath();
    }

}
