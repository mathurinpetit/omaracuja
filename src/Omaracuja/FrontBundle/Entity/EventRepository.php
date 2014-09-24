<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository {

    public function findAllOrderedByDate() {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }
    
//    public function findUserProposedEvent($user) {
//        $qb = $this->createQueryBuilder('e');
//        $qb->where('e.createdAt', 'DESC');
//        $qb->orderBy('bp.createdAt', 'DESC');
//        return $qb->getQuery()->getResult();
//    }

}
