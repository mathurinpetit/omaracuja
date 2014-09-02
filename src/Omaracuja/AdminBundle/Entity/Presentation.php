<?php

namespace Omaracuja\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_presentation")
 */
class Presentation {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var text $title
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var text $paragraph1
     *
     * @ORM\Column(name="paragraph1", type="text", nullable=false)
     */
    private $paragraph1;

    /**
     * @var text $paragraph2
     *
     * @ORM\Column(name="paragraph2", type="text", nullable=true)
     */
    private $paragraph2;

    /**
     * @var text $paragraph3
     *
     * @ORM\Column(name="paragraph3", type="text", nullable=true)
     */
    private $paragraph3;

    /**
     * @var text $map_link
     *
     * @ORM\Column(name="map_link", type="text", nullable=true)
     */
    private $map_link;

    /**
     * @var text $adresse
     *
     * @ORM\Column(name="adresse", type="text", nullable=true)
     */
    private $adresse;

    /**
     * @var text $contact
     *
     * @ORM\Column(name="contact", type="text", nullable=true)
     */
    private $contact;

    /**
     * @var text $fb_link
     *
     * @ORM\Column(name="fb_link", type="text", nullable=true)
     */
    private $fb_link;
    
    /**
     * @var boolean $selected
     *
     * @ORM\Column(name="selected", type="boolean", nullable=false)
     */
    private $selected;


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
     * Set title
     *
     * @param string $title
     * @return Presentation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set paragraph1
     *
     * @param string $paragraph1
     * @return Presentation
     */
    public function setParagraph1($paragraph1)
    {
        $this->paragraph1 = $paragraph1;

        return $this;
    }

    /**
     * Get paragraph1
     *
     * @return string 
     */
    public function getParagraph1()
    {
        return $this->paragraph1;
    }

    /**
     * Set paragraph2
     *
     * @param string $paragraph2
     * @return Presentation
     */
    public function setParagraph2($paragraph2)
    {
        $this->paragraph2 = $paragraph2;

        return $this;
    }

    /**
     * Get paragraph2
     *
     * @return string 
     */
    public function getParagraph2()
    {
        return $this->paragraph2;
    }

    /**
     * Set paragraph3
     *
     * @param string $paragraph3
     * @return Presentation
     */
    public function setParagraph3($paragraph3)
    {
        $this->paragraph3 = $paragraph3;

        return $this;
    }

    /**
     * Get paragraph3
     *
     * @return string 
     */
    public function getParagraph3()
    {
        return $this->paragraph3;
    }

    /**
     * Set map_link
     *
     * @param string $mapLink
     * @return Presentation
     */
    public function setMapLink($mapLink)
    {
        $this->map_link = $mapLink;

        return $this;
    }

    /**
     * Get map_link
     *
     * @return string 
     */
    public function getMapLink()
    {
        return $this->map_link;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Presentation
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return Presentation
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set fb_link
     *
     * @param string $fbLink
     * @return Presentation
     */
    public function setFbLink($fbLink)
    {
        $this->fb_link = $fbLink;

        return $this;
    }

    /**
     * Get fb_link
     *
     * @return string 
     */
    public function getFbLink()
    {
        return $this->fb_link;
    }

    /**
     * Set selected
     *
     * @param integer $selected
     * @return Presentation
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     *
     * @return integer 
     */
    public function getSelected()
    {
        return $this->selected;
    }
    
    public function isSelected() {
        return $this->getSelected();
    }
    
    public function select(){
        $this->selected = true;
    }
    
    public function unselect(){
        $this->selected = false;
    }
}
