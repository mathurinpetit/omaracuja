<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Omaracuja\FrontBundle\Entity\EventAlbum as EventAlbum;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_picture")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\PictureRepository")
 */
class Picture {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var datetime $createdAt     
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *       
     */
    private $createdAt;

    /**
     * @Assert\File(maxSize = "8M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/bmp", "image/x-ms-bmp"},
     *     mimeTypesMessage = "Choisissez un fichier jpg, png, gif ou bmp"
     * )
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Omaracuja\FrontBundle\Entity\EventAlbum", inversedBy="pictures")
     */
    private $album;

    public function __construct(EventAlbum $album) {
        $this->createdAt = new DateTime();
        $this->desks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->album = $album;
    }

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

    private $src = null;
    private $data = null;
    private $ajaxMsg = null;
    private $type = null;

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
        return null === $this->getPath() ? null : $prefix . $this->getUploadDir() . '/photo_' . $this->getId() . '/original/' . $this->getPath();
    }

    public function getWebPath($prefix = '/') {
        return null === $this->getPath() ? null : $prefix . $this->getUploadDir() . '/photo_' . $this->getId() . '/' . $this->getPath();
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir() . '/photo_' . $this->getId() . '/original/';
    }

    protected function getUploadDir() {
        return 'data/pictures';
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Event
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

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getCurrentPicturePath() {
        if (!$this->getWebPath()) {
            return "/data/pictures/omaracuja_default_picture.jpg";
        }
        return $this->getWebPath();
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
                $this->ajaxMsg = "Immposible de lire le fichier";
                return;
            }

            $dst_img = imagecreatetruecolor($data->width, $data->height);

            if ($this->type == IMAGETYPE_GIF or $this->type == IMAGETYPE_PNG) {
                imagecolortransparent($dst_img, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
                imagealphablending($dst_img, false);
                imagesavealpha($dst_img, true);
            }
            $result = imagecopyresampled($dst_img, $src_img, 0, 0, $data->x, $data->y, $data->width, $data->height, $data->width, $data->height);

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
                    $this->ajaxMsg = "Impossible de sauvegarder l'image.";
                }
            } else {
                $this->ajaxMsg = "Impossible de transformer l'image.";
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

    public function getResult() {
        return !empty($this->data) ? $this->getWebPath() : $this->getWebOriginalPath();
    }

    public function getAjaxMsg() {
        return $this->ajaxMsg;
    }

    public function __toString() {
        return $this->getWebPath();
    }

    public function getAlbum() {
        return $this->album;
    }

    public function unlink() {
        $pathToRemove = $this->getWebPath();
        if (file_exists($pathToRemove) &&
                is_writable($pathToRemove)) {
            unlink($pathToRemove);
        }
    }


    /**
     * Set album
     *
     * @param \Omaracuja\FrontBundle\Entity\EventAlbum $album
     * @return Picture
     */
    public function setAlbum(\Omaracuja\FrontBundle\Entity\EventAlbum $album = null)
    {
        $this->album = $album;

        return $this;
    }
}
