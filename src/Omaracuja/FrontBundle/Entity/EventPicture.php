<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_event_picture")
 * @ORM\HasLifecycleCallbacks
 */
class EventPicture {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


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

        $originalFile = $this->getWebOriginalPath("");
        if ($originalFile) {
            unlink($originalFile);
        }
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
    private $type = null;

    const HEIGHT = 220;
    const WIDTH = 220;

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
        return null === $this->getPath() ? null : $prefix . $this->getUploadDir() . '/event_' . $this->event->getId() . '/original/' . $this->getPath();
    }

    public function getWebPath($prefix = '/') {
        return null === $this->getPath() ? null : $prefix . $this->getUploadDir() . '/event_' . $this->event->getId() . '/' . $this->getPath();
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir() . '/event_' . $this->event->getId() . '/original/';
    }

    protected function getUploadDir() {
        return 'data/avatars';
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
        if (!empty($src) && !empty($dst) && !empty($data)) {
            $src_img = null;
            $this->type = exif_imagetype($src);

            switch ($this->type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
                case IMAGETYPE_BMP:
                    $src_img = imagecreatefromwbmp($src);
                    break;
            }

            if (!$src_img) {
                $this->ajaxMsg = "Failed to read the image file";
                return;
            }

            $dst_img = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

            if ($this->type == IMAGETYPE_GIF or $this->type == IMAGETYPE_PNG) {
                imagecolortransparent($dst_img, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
                imagealphablending($dst_img, false);
                imagesavealpha($dst_img, true);
            }

            $result = imagecopyresampled($dst_img, $src_img, 0, 0, $data->x, $data->y, self::WIDTH, self::HEIGHT, $data->width, $data->height);

            if ($result) {
                switch ($this->type) {
                    case IMAGETYPE_GIF:
                        $result = imagegif($dst_img, $dst);
                        break;

                    case IMAGETYPE_JPEG:
                        $result = imagejpeg($dst_img, $dst);
                        break;

                    case IMAGETYPE_PNG:
                        $result = imagepng($dst_img, $dst);
                        break;

                    case IMAGETYPE_BMP:
                        $result = imagewbmp($dst_img, $dst);
                        break;
                }

                if (!$result) {
                    $this->ajaxMsg = "Failed to save the cropped image file";
                }
            } else {
                $this->ajaxMsg = "Failed to crop the image file";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
            return true;
        }
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
        return !empty($this->data) ? $this->getWebPath() : $this->getWebOriginalPath();
    }

    public function getAjaxMsg() {
        return $this->ajaxMsg;
    }

    public function __toString() {
        return $this->getWebPath();
    }

}
