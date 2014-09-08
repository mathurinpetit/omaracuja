<?php
namespace Omaracuja\FrontBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Omaracuja\UserBundle\Entity\User as User;

use \DateTime;
/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_blog_post")
 */
class BlogPost
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
     * @ORM\ManyToOne(targetEntity="Omaracuja\UserBundle\Entity\User", inversedBy="blogPosts")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false)
     */
    private $author;

    /**
     * @var text $content
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

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

    

    public function __construct(User $user) {
        $this->author = $user;
        $this->createdAt = new DateTime();
        $this->public = true;
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
     * Set group
     *
     * @param string $group
     * @return BlogPost
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return BlogPost
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return BlogPost
     */
    public function setContent($content)
    {
        $content = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i"," <object width=\"100%\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/$1&hl=en&fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><embed src=\"http://www.youtube.com/v/$1&hl=en&fs=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" width=\"100%\" height=\"450\"></embed></object>  ",$content);
        $this->content = $content;
        
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return BlogPost
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
     * Set public
     *
     * @param boolean $public
     * @return BlogPost
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }
}
