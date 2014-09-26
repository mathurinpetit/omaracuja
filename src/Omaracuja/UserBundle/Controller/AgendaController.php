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

        $nextEvents = array();
        foreach ($eventsProposed as $eventProposed) {
            $localEvent = new \stdClass();
            $localEvent->event = $eventProposed;
            $localEvent->accepted = in_array($eventProposed, $eventsAccepted->toArray());
            $startDate = $localEvent->event->getStartAt();
            $today = new \DateTime();
            if($startDate >= $today){
            $nextEvents[$startDate->format('YmdHi')] = $localEvent;                
            }            
        }
        krsort($nextEvents);
        return $this->render('OmaracujaUserBundle:Agenda:agenda.html.twig', array(
                    'events' => $nextEvents,
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
