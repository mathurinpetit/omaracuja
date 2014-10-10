<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\FronBundle\Entity\Event as Event;

class AgendaController extends Controller {

    public function agendaAction(Request $request) {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $eventsProposed = $user->getProposedEvents();
        $eventsAccepted = $user->getParticipateEvents();
        $eventsRefused = $user->getRefusedEvents();
        $eventsProposedByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->sortEventsByIsoMonth($eventsProposed);

        $nextEvents = array();
        foreach ($eventsProposedByMonth as $month => $eventsProposed) {
            $localEventsArray = array();
            foreach ($eventsProposed as $eventProposed) {
                $localEvent = new \stdClass();
                $localEvent->event = $eventProposed;
                $localEvent->accepted = in_array($eventProposed, $eventsAccepted->toArray());
                $localEvent->refused = in_array($eventProposed, $eventsRefused->toArray());
                $startDate = $localEvent->event->getStartAt();
                $today = new \DateTime();
                if ($startDate >= $today) {
                    $localEventsArray[$startDate->format('YmdHi')] = $localEvent;
                }
            }
            if (count($localEventsArray)) {
                $nextEvents[$month] = array();
                krsort($localEventsArray);
                $nextEvents[$month] = $localEventsArray;
            }
        }
        krsort($nextEvents);
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        return $this->render('OmaracujaUserBundle:Agenda:agenda.html.twig', array(
                    'pastEvent' => false,
                    'events' => $nextEvents,
                    'user' => $user,
        ));
    }

    public function eventsPastAction(Request $request) {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $eventsProposed = $user->getProposedEvents();
        $eventsAccepted = $user->getParticipateEvents();

        $eventsProposedByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->sortEventsByIsoMonth($eventsProposed);

        $lastEvents = array();
        foreach ($eventsProposedByMonth as $month => $eventsProposed) {
            $localEventsArray = array();
            foreach ($eventsProposed as $eventProposed) {
                $localEvent = new \stdClass();
                $localEvent->event = $eventProposed;
                $localEvent->accepted = in_array($eventProposed, $eventsAccepted->toArray());
                $startDate = $localEvent->event->getStartAt();
                $today = new \DateTime();
                if ($startDate < $today) {
                    $localEventsArray[$startDate->format('YmdHi')] = $localEvent;
                }
            }
            if (count($localEventsArray)) {
                $lastEvents[$month] = array();
                krsort($localEventsArray);
                $lastEvents[$month] = $localEventsArray;
            }
        }
        krsort($lastEvents);
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        return $this->render('OmaracujaUserBundle:Agenda:agenda.html.twig', array(
                    'pastEvent' => true,
                    'events' => $lastEvents,
                    'user' => $user,
        ));
    }

    public function eventAcceptAction(Request $request, $idEvent) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->find($idEvent);
        $event->addActualTeam($user);
        $em->persist($event);
        $em->flush();
        return $this->redirect($this->generateUrl('compte_agenda'));
    }

}
