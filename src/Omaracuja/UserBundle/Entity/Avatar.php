<?php

namespace Omaracuja\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Omaracuja\UserBundle\Entity\User as User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_avatar")
 * @ORM\HasLifecycleCallbacks
 */
class Avatar {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="avatars")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            $this->setPath(sha1(uniqid(mt_rand(), true)) . '.' . $this->getFile()->guessExtension());
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getFile()) {
            return;
        }

        $this->getFile()->move($this->getUploadRootDir(), $this->getPath());

        $this->setFile(null);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function getAbsolutePath() {
        return null === $this->getPath() ? null : $this->getUploadRootDir() . '/' . $this->getPath();
    }

    public function getWebPath() {
        return null === $this->getPath() ? null : '/'.$this->getUploadDir() . '/user_' . $this->user->getId() . '/' . $this->getPath();
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir() . '/user_' . $this->user->getId() . '/';
    }

    protected function getUploadDir() {
        return 'data/avatars';
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
    }

}
