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
        
        $this->image_resize($this->getWebOriginalPath(""), $this->getWebPath(""), 700, 700);
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

    public function getWebOriginalPath($prefix = '/') {
        return null === $this->getPath() ? null : $prefix . $this->getUploadDir() . '/user_' . $this->user->getId() . '/original/' . $this->getPath();
    }
    
    public function getWebPath($prefix = '/') {
        return null === $this->getPath() ? null : $prefix . $this->getUploadDir() . '/user_' . $this->user->getId() . '/' . $this->getPath();
    }
    

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir() . '/user_' . $this->user->getId() . '/original/';
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

    // Gestion d'image
    public function image_resize($src, $dst, $width, $height, $crop = 0) {

        if (!list($w, $h) = getimagesize($src)){
            return "Unsupported picture type!";
        }
        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg') {
            $type = 'jpg';
        }
        switch ($type) {
            case 'bmp': $img = imagecreatefromwbmp($src);
                break;
            case 'gif': $img = imagecreatefromgif($src);
                break;
            case 'jpg': $img = imagecreatefromjpeg($src);
                break;
            case 'png': $img = imagecreatefrompng($src);
                break;
            default : return "Unsupported picture type!";
        }

        // resize
        if ($crop) {
            if ($w < $width or $h < $height){
                return "Picture is too small!";
            }
            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else {
            if ($w < $width and $h < $height){
                return "Picture is too small!";
            }
            $ratio = min($width / $w, $height / $h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);
        
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch ($type) {
            case 'bmp': imagewbmp($new, $dst);
                break;
            case 'gif': imagegif($new, $dst);
                break;
            case 'jpg': imagejpeg($new, $dst);
                break;
            case 'png': imagepng($new, $dst);
                break;
        }
        return true;
    }

}
