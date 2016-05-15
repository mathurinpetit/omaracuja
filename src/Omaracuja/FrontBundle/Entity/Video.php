<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_video")
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\VideoRepository")
 */
class Video {

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
     * @var text $url
     *
     * @ORM\Column(name="url", type="string", length=1024, nullable=false)
     */
    private $url;

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

    public function __construct() {
        $this->createdAt = new DateTime();
        $this->public = true;
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
     * Set content
     *
     * @param string $url
     * @return BlogPost
     */
    public function setUrl($url) {
        $url = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
                " <object width=\"100%\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/$1&hl=en&fs=1?wmode=transparent\"  frameborder=\"0\" wmode=\"Opaque\">".
                "</param><param name=\"allowFullScreen\" value=\"true\"></param><embed src=\"http://www.youtube.com/v/$1&hl=en&fs=1\?wmode=transparent\"  frameborder=\"0\" wmode=\"Opaque\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" width=\"100%\" height=\"450\"></embed></object>  ", $url);
        $this->url = $url;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return BlogPost
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

    public function getCreatedAtFr() {
        $d = $this->getCreatedAt();
        if ($d) {
            return $d->format('d M Y H:i');
        }
        return '';
    }

    public function setCreatedAtFr($dateString) {
        $date = DateTime::createFromFormat('d M Y H:i', $dateString);
        $d = $this->setCreatedAt($date);
        return $this;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return BlogPost
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
     * Set title
     *
     * @param string $title
     * @return BlogPost
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

}
