<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;
use Omaracuja\AdminBundle\Form\PresentationType as PresentationType;
use Omaracuja\FrontBundle\Entity\Event as Event;
use Omaracuja\FrontBundle\Form\EventType as EventType;

class AdminController extends Controller {

    /**
     * @Template()
     */
    public function userPanelAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OmaracujaUserBundle:User')->findAll();
        return array('users' => $entities);
    }

    /**
     * @Template()
     */
    public function presentationPanelAction() {
        $em = $this->getDoctrine()->getManager();
        $presentations = $em->getRepository('OmaracujaAdminBundle:Presentation')->findAll();
        $actual_presentation = null;
        foreach ($presentations as $presentation) {
            if ($presentation->isSelected()) {
                $actual_presentation = $presentation;
            }
        }

        return array('presentations' => $presentations, 'actual_presentation' => $actual_presentation);
    }

    /**
     * @Template()
     */
    public function presentationNewAction() {
        $presentation = new Presentation();

        $form = $this->createForm(new PresentationType(), $presentation, array(
            'action' => $this->generateUrl('admin_create_presentation'),
            'method' => 'POST',
        ));

        return array(
            'presentation' => $presentation,
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     */
    public function presentationCreateAction(Request $request) {
        $presentation = new Presentation();
        $form = $this->createForm(new PresentationType(), $presentation, array(
            'action' => $this->generateUrl('admin_create_presentation'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $presentation->select();
            $em->persist($presentation);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_presentation'));
        }

        return array(
            'entity' => $presentation,
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     */
    public function eventPanelAction() {

        //EvennementCreation
        $newEvent = new Event();
        $newEventForm = $this->createForm(new EventType(), $newEvent, array(
            'action' => $this->generateUrl('admin_event_create'),
            'method' => 'POST',
        ));
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('OmaracujaFrontBundle:Event')->findAll();
        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array('events' => $events, 'newEvent' => $newEvent, 'newEventForm' => $newEventForm->createView()));
    }
    
    public function eventCreateAction(Request $request) {
        
        $newEvent = new Event();
        $newEventForm = $this->createForm(new EventType(), $newEvent, array(
            'action' => $this->generateUrl('admin_event_create'),
            'method' => 'POST',
        ));

        $newEventForm->handleRequest($request);

        if ($newEventForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newEvent);
            $em->flush();

            return $this->redirect($this->generateUrl('front_evennement'));
        }

        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array(
                    'newEvent' => $newEvent,
                    'newEventForm' => $newEventForm->createView(),
        ));
    }

    public function userActivateAction($userId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->activate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel_users'));
    }

    public function userDesactivateAction($userId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->desactivate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel_users'));
    }

}
