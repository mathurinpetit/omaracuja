<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PictureRepository extends EntityRepository {

   public function findAllOrderedByDate() {
        $qb = $this->createQueryBuilder('e');        
        $qb->orderBy('e.createdAt', 'DESC');
        return $this->sortPicturesByMonth($qb->getQuery()->getResult());
    }
    
    public function sortPicturesByMonth($picturesArray) {
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        $monthPicturesArray = array();
        foreach ($picturesArray as $picture) {
            $createDate = $picture->getCreatedAt();
            $month_key = ucfirst(strftime("%B %Y", strtotime($createDate->format('Y-m-d'))));
            if (!array_key_exists($month_key, $monthPicturesArray)) {
                $monthPicturesArray[$month_key] = array();
            }
            $monthPicturesArray[$month_key][] = $picture;
        }
        return $monthPicturesArray;
    }
}
