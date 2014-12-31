<?php

/**
 * Description of EventAlbum
 *
 * @author mathurin
 */

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Omaracuja\UserBundle\Entity\User as User;
use Symfony\Component\Validator\Constraints as Assert;
use Omaracuja\FrontBundle\Entity\EventPicture as EventPicture;
use Omaracuja\FrontBundle\Entity\EventAlbum as EventAlbum;
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
     * @var text $public_description     
     * @ORM\Column(name="public_description", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $public_description;
    
        /**
     * @var text $private_description     
     * @ORM\Column(name="private_description", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $private_description;

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
     * @ORM\ManyToMany(targetEntity="Omaracuja\UserBundle\Entity\User", inversedBy="refusedEvents")
     * @ORM\JoinTable(name="omaracuja_user_refuse_event")
     */
    private $refusedUsers;

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

    /**
     * @ORM\OneToOne(targetEntity="Omaracuja\FrontBundle\Entity\EventAlbum")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    private $album;

    public function __construct(User $user) {
        $this->createdAt = new DateTime();
        $this->public = true;
        $this->startAt = new DateTime();
        $this->proposedTeam = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actualTeam = new \Doctrine\Common\Collections\ArrayCollection();
        $this->refusedUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->addProposedTeam($user);
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

    public function getStartAtFr() {
        $d = $this->getStartAt();
        if ($d) {
            return $d->format('d M Y H:i');
        }
        return '';
    }

    public function setStartAtFr($dateString) {
        $date = DateTime::createFromFormat('d M Y H:i', $dateString);
        $d = $this->setStartAt($date);
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

    public function getEndAtFr() {
        $d = $this->getEndAt();
        if ($d) {
            return $d->format('d M Y H:i');
        }
        return '';
    }

    public function setEndAtFr($dateString) {
        $date = DateTime::createFromFormat('d M Y H:i', $dateString);
        $d = $this->setEndAt($date);
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
     * Add refusedUsers
     *
     * @param \Omaracuja\UserBundle\Entity\User $refusedUser
     * @return Event
     */
    public function addRefusedUsers(\Omaracuja\UserBundle\Entity\User $refusedUser) {
        $this->refusedUsers[] = $refusedUser;

        return $this;
    }

    /**
     * Remove refusedUsers
     *
     * @param \Omaracuja\UserBundle\Entity\User $refusedUser
     */
    public function removeRefusedUsers(\Omaracuja\UserBundle\Entity\User $refusedUser) {
        $this->refusedUsers->removeElement($refusedUser);
    }

    /**
     * Get refusedUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRefusedUsers() {
        return $this->refusedUsers;
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
        return $this->eventPicture->getWebPath();
    }


    /**
     * Set public_description
     *
     * @param string $publicDescription
     * @return Event
     */
    public function setPublicDescription($publicDescription)
    {
        $this->public_description = $publicDescription;

        return $this;
    }

    /**
     * Get public_description
     *
     * @return string 
     */
    public function getPublicDescription()
    {
        return $this->public_description;
    }

    /**
     * Set private_description
     *
     * @param string $privateDescription
     * @return Event
     */
    public function setPrivateDescription($privateDescription)
    {
        $this->private_description = $privateDescription;

        return $this;
    }

    /**
     * Get private_description
     *
     * @return string 
     */
    public function getPrivateDescription()
    {
        return $this->private_description;
    }

    /**
     * Add refusedUsers
     *
     * @param \Omaracuja\UserBundle\Entity\User $refusedUsers
     * @return Event
     */
    public function addRefusedUser(\Omaracuja\UserBundle\Entity\User $refusedUsers)
    {
        $this->refusedUsers[] = $refusedUsers;

        return $this;
    }

    /**
     * Remove refusedUsers
     *
     * @param \Omaracuja\UserBundle\Entity\User $refusedUsers
     */
    public function removeRefusedUser(\Omaracuja\UserBundle\Entity\User $refusedUsers)
    {
        $this->refusedUsers->removeElement($refusedUsers);
    }

    /**
     * Set eventPicture
     *
     * @param \Omaracuja\FrontBundle\Entity\EventPicture $eventPicture
     * @return Event
     */
    public function setEventPicture(\Omaracuja\FrontBundle\Entity\EventPicture $eventPicture = null)
    {
        $this->eventPicture = $eventPicture;

        return $this;
    }

    /**
     * Get eventPicture
     *
     * @return \Omaracuja\FrontBundle\Entity\EventPicture 
     */
    public function getEventPicture()
    {
        return $this->eventPicture;
    }

    /**
     * Set album
     *
     * @param \Omaracuja\FrontBundle\Entity\EventAlbum $album
     * @return Event
     */
    public function setAlbum(\Omaracuja\FrontBundle\Entity\EventAlbum $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \Omaracuja\FrontBundle\Entity\EventAlbum 
     */
    public function getAlbum()
    {
        return $this->album;
    }
    
    public function isOnlyOneDay() {
        var_dump($this->getEndAt()); exit;
    }
    
    
    public function getNonAnswerUsers() {
        $proposedTeam = $this->getProposedTeam();
        $actualTeam = $this->getActualTeam()->toArray();
        $refusedUser = $this->getRefusedUsers()->toArray();
        
        $nonAnswerUsers = array();
        foreach ($proposedTeam as $key => $proposedUser) {
            if(!in_array($proposedUser, $actualTeam) && !in_array($proposedUser, $refusedUser)){
                $nonAnswerUsers[] = $proposedUser;
            }
        }
        
        return $nonAnswerUsers;
    }
}
