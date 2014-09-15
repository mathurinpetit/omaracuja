<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Omaracuja\UserBundle\Entity\User as User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="omaracuja_event")
 * @ORM\Entity(repositoryClass="Omaracuja\FrontBundle\Entity\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event {

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
     * @ORM\Column(name="title", type="string", length=1024, nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var text $description     
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var datetime $createdAt     
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *       
     */
    private $createdAt;

    /**
     * @var boolean $public
     *
     * @ORM\Column(name="public", type="boolean", nullable=true)
     */
    private $public;

    /**
     * @var datetime $startAt    
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     * @Assert\NotBlank()
     */
    private $startAt;

    /**
     * @var datetime $endAt
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var string $place
     * 
     * @ORM\Column(name="place", type="string", length=1024, nullable=false)
     * @Assert\NotBlank()
     */
    private $place;

    /**
     * @ORM\ManyToMany(targetEntity="Omaracuja\UserBundle\Entity\User", inversedBy="proposedEvents")
     * @ORM\JoinTable(name="omaracuja_user_proposed_event")
     */
    private $proposedTeam;

    /**
     * @ORM\ManyToMany(targetEntity="Omaracuja\UserBundle\Entity\User", inversedBy="participateEvents")
     * @ORM\JoinTable(name="omaracuja_user_participate_event")
     */
    private $actualTeam;

    /**
     * @var float $mapX
     *
     * @ORM\Column(name="map_x", type="float", nullable=true)
     */
    private $mapX;

    /**
     * @var float $mapY
     *
     * @ORM\Column(name="map_y", type="float", nullable=true)
     */
    private $mapY;    
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;



    public function __construct() {
        $this->createdAt = new DateTime();
        $this->public = true;
        $this->proposedTeam = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actualTeam = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Event
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

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
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

    /**
     * Set public
     *
     * @param boolean $public
     * @return Event
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
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return Event
     */
    public function setStartAt($startAt) {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt() {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Event
     */
    public function setEndAt($endAt) {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt() {
        return $this->endAt;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return Event
     */
    public function setPlace($place) {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace() {
        return $this->place;
    }

    /**
     * Add proposedTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $proposedTeam
     * @return Event
     */
    public function addProposedTeam(\Omaracuja\UserBundle\Entity\User $proposedTeam) {
        $this->proposedTeam[] = $proposedTeam;

        return $this;
    }

    /**
     * Remove proposedTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $proposedTeam
     */
    public function removeProposedTeam(\Omaracuja\UserBundle\Entity\User $proposedTeam) {
        $this->proposedTeam->removeElement($proposedTeam);
    }

    /**
     * Get proposedTeam
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProposedTeam() {
        return $this->proposedTeam;
    }

    /**
     * Add actualTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $actualTeam
     * @return Event
     */
    public function addActualTeam(\Omaracuja\UserBundle\Entity\User $actualTeam) {
        $this->actualTeam[] = $actualTeam;

        return $this;
    }

    /**
     * Remove actualTeam
     *
     * @param \Omaracuja\UserBundle\Entity\User $actualTeam
     */
    public function removeActualTeam(\Omaracuja\UserBundle\Entity\User $actualTeam) {
        $this->actualTeam->removeElement($actualTeam);
    }

    /**
     * Get actualTeam
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActualTeam() {
        return $this->actualTeam;
    }

    /**
     * Set mapX
     *
     * @param float $mapX
     * @return Event
     */
    public function setMapX($mapX) {
        $this->mapX = $mapX;

        return $this;
    }

    /**
     * Get mapX
     *
     * @return float 
     */
    public function getMapX() {
        return $this->mapX;
    }

    /**
     * Set mapY
     *
     * @param float $mapY
     * @return Event
     */
    public function setMapY($mapY) {
        $this->mapY = $mapY;

        return $this;
    }

    /**
     * Get mapY
     *
     * @return float 
     */
    public function getMapY() {
        return $this->mapY;
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

    const HEIGHT = 600;
    const WIDTH = 400;

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
        return 'data/events';
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
        return $this->title();
    }

    public function getPicturePath() {
        if (!$this->path) {
            return "/data/events/omaracuja_event_no_picture.jpg";
        }
        return $this->getWebPath();
    }

}
