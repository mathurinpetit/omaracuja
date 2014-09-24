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
        
        $events = $user->getProposedEvents();        
        
        return $this->render('OmaracujaUserBundle:Agenda:agenda.html.twig', array(
            'events' => $events,
        ));
    }
    
}
