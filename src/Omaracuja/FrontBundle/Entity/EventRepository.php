<?php

namespace Omaracuja\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Omaracuja\FrontBundle\Entity\EventAlbum as EventAlbum;

class EventRepository extends EntityRepository {

    public function findAllWithAlbumOrderedByDate() {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.album IS NOT NULL');
        $qb->orderBy('e.startAt', 'DESC');
        return $this->sortEventsByMonth($qb->getQuery()->getResult());
    }

    public function findNextOrderedByDate($restrictPublic = false, $chronologique_order = false) {
        $qb = $this->createQueryBuilder('e');
      //  $qb->where('e.startAt >= CURRENT_DATE()');
        if ($restrictPublic) {
            $qb->andWhere('e.public = 1');
        }
        if ($chronologique_order) {
            $qb->orderBy('e.startAt', 'ASC');
        } else {
            $qb->orderBy('e.startAt', 'DESC');
        }
        return $this->sortEventsByMonth($qb->getQuery()->getResult());
    }

    public function findPastEventOrderedByDate($restrictPublic = false, $chronologique_order = false) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.startAt < CURRENT_DATE()');
        if ($restrictPublic) {
            $qb->andWhere('e.public = 1');
        }
        if ($chronologique_order) {
            $qb->orderBy('e.startAt', 'ASC');
        } else {
            $qb->orderBy('e.startAt', 'DESC');
        }
        return $this->sortEventsByMonth($qb->getQuery()->getResult());
    }

    public function sortEventsByMonth($eventsArray) {
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        $monthEventsArray = array();
        foreach ($eventsArray as $events) {
            $startDate = $events->getStartAt();
            $month_key = ucfirst(strftime("%B %Y", strtotime($startDate->format('Y-m-d'))));
            if (!array_key_exists($month_key, $monthEventsArray)) {
                $monthEventsArray[$month_key] = array();
            }
            $monthEventsArray[$month_key][] = $events;
        }
        return $monthEventsArray;
    }

    public function sortEventsByIsoMonth($eventsArray) {
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        $monthEventsArray = array();
        foreach ($eventsArray as $events) {
            $startDate = $events->getStartAt();
            $month_key = ucfirst(strftime("%Y%m", strtotime($startDate->format('Y-m-d'))));
            if (!array_key_exists($month_key, $monthEventsArray)) {
                $monthEventsArray[$month_key] = array();
            }
            $monthEventsArray[$month_key][] = $events;
        }
        return $monthEventsArray;
    }

    public function findAlbumEventOrCreate($event) {
        $album = $event->getAlbum();
        if (!$album) {
            return new EventAlbum($event->getId());
        }
        return $album;
    }

}
