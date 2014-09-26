<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository {

    public function findAllOrderedByDate() {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.endAt >= CURRENT_DATE()');
        $qb->orderBy('e.startAt', 'DESC');
        return $qb->getQuery()->getResult();
    }  

}
