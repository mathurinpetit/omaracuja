<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository {

    public function findAllOrderedByDate() {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }  

}
