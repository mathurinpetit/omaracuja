<?php

/**
 * Description of EventAlbum
 *
 * @author mathurin
 */

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Omaracuja\FrontBundle\Entity\Picture as Picture;

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

}
