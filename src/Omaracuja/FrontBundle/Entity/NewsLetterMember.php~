<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_newsletter_members")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\NewsletterMemberRepository")
 */
class NewsLetterMember {

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var datetime $createdAt     
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *       
     */
    private $createdAt;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="text", nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;
    
     /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     */
    protected $xkey;


    public function __construct() {
        $this->createdAt = new DateTime();
        $this->xkey = md5(microtime().rand());
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
     * Set name
     *
     * @param string $name
     * @return NewsLetterMember
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return NewsLetterMember
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return NewsLetterMember
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
    
    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return NewsLetterMember
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get xkey
     *
     * @return string 
     */
    public function getXkey()
    {
        return $this->xkey;
    }

    /**
     * Set email
     *
     * @param string $xkey
     * @return NewsLetterMember
     */
    public function setXkey($xkey)
    {
        $this->xkey = $xkey;

        return $this;
    }

}
