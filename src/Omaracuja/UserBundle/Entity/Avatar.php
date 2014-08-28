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

        $this->image_resize($this->getWebOriginalPath(""), $this->getWebPath(""), $this->data);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    private $src = null;
    private $data = null;
    private $ajaxMsg = null;

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
    public function image_resize($src, $dst, $data) {

        if (!list($w, $h) = getimagesize($src)) {
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

        if ($w < $data->width or $h < $data->height) {
            return "Picture is too small!";
        }
        $ratio = max($data->width / $w, $data->height / $h);
        $h = $data->height / $ratio;
        $x = ($w - $data->width / $ratio) / 2;
        $w = $data->width / $ratio;

        $new = imagecreatetruecolor($data->width, $data->height);

        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        $result = imagecopyresampled($new, $img, 0, 0, $data->x, $data->y, 220, 220, $data->width, $data->height);

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
        if (!$result) {
            $this->ajaxMsg = "Failed to save the cropped image file";
        } else {
            $this->ajaxMsg = "Failed to crop the image file";
        }

        imagedestroy($new);
        imagedestroy($img);
        return true;
    }

    public function setData($data) {
        if (!empty($data)) {
            $this->data = json_decode(stripslashes($data));
        }
    }

    public function getSrc() {
        return $this->src;
    }

    public function setSrc($src) {
        if (!empty($src)) {
            $type = exif_imagetype($src);

            if ($type) {
                $this->src = $src;
                $this->type = $type;
                $this->extension = image_type_to_extension($type);
                $this->setDst();
            }
        }
    }

    public function getData() {
        return $this->data;
    }

    private function codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                break;

            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                break;

            case UPLOAD_ERR_PARTIAL:
                $message = 'The uploaded file was only partially uploaded';
                break;

            case UPLOAD_ERR_NO_FILE:
                $message = 'No file was uploaded';
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Missing a temporary folder';
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk';
                break;

            case UPLOAD_ERR_EXTENSION:
                $message = 'File upload stopped by extension';
                break;

            default:
                $message = 'Unknown upload error';
        }

        return $message;
    }

    public function getResult() {
        return !empty($this->data) ? $this->getWebPath("") : $this->getWebOriginalPath("");
    }

    public function getAjaxMsg() {
        return $this->ajaxMsg;
    }

}
