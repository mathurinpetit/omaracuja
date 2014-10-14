<?php

/**
 * Description of EventAlbum
 *
 * @author mathurin
 */

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Omaracuja\FrontBundle\Entity\Picture as Picture;
use Omaracuja\FrontBundle\Entity\Event as Event;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_event_album")
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\EventAlbumRepository")
 */
class EventAlbum {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Omaracuja\FrontBundle\Entity\Picture", mappedBy="album", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="omaracuja_album_picture")
     */
    private $pictures;

    public function __construct() {
        $this->pictures = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add pictures
     *
     * @param \Omaracuja\FrontBundle\Entity\Picture $pictures
     * @return EventAlbum
     */
    public function addPicture(\Omaracuja\FrontBundle\Entity\Picture $pictures)
    {
        $this->pictures[] = $pictures;

        return $this;
    }

    /**
     * Remove pictures
     *
     * @param \Omaracuja\FrontBundle\Entity\Picture $pictures
     */
    public function removePicture(\Omaracuja\FrontBundle\Entity\Picture $pictures)
    {
        $this->pictures->removeElement($pictures);
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPictures()
    {
        return $this->pictures;
    }
}
