<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository {

    public function findAllOrderedByDate() {
        $qb = $this->createQueryBuilder('bp');
        $qb->orderBy('bp.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }

}
