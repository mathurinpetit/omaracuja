<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BlogPostRepository extends EntityRepository {

    public function findAllOrderedByDate() {
        $qb = $this->createQueryBuilder('bp');
        $qb->orderBy('bp.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }
    
    public function findAllOrderedByDateWithLimit($limit = 3) {
        $qb = $this->createQueryBuilder('bp');
        $qb->orderBy('bp.createdAt', 'DESC');
        $qb->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

    public function findPublicOrderedByDateWithLimit($limit = 3) {
        $qb = $this->createQueryBuilder('bp');
        $qb->where('bp.public = 1');
        $qb->orderBy('bp.createdAt', 'DESC');
        $qb->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }
    
}
