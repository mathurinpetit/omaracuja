<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;
use Omaracuja\AdminBundle\Form\PresentationType as PresentationType;
use Omaracuja\FrontBundle\Entity\Event as Event;
use Omaracuja\FrontBundle\Entity\EventPicture as EventPicture;
use Omaracuja\FrontBundle\Entity\Picture as Picture;
use Omaracuja\FrontBundle\Form\EventType as EventType;

class AdminController extends Controller {

    /**
     * @Template()
     */
    public function userPanelAction() {
        $em = $this->getDoctrine()->getManager();
        $usersAdmin = $em->getRepository('OmaracujaUserBundle:User')->findByRole('ROLE_ADMIN');
        $usersNoAdmin = $em->getRepository('OmaracujaUserBundle:User')->findByHasNotRole('ROLE_ADMIN');
        return array('usersAdmin' => $usersAdmin, 'usersNoAdmin' => $usersNoAdmin);
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

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $eventsAccepted = $user->getParticipateEvents();
        $nextEventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate();

        $eventPictureForms = array();
        foreach ($nextEventsByMonth as $month => $nextEvents) {
            foreach ($nextEvents as $nextEvent) {
                $eventPicture = new EventPicture();
                $formFactory = $this->container->get('form.factory');

                $eventPictureFormBuilder = $formFactory->createBuilder('form', $eventPicture);
                $eventPictureForm = $eventPictureFormBuilder->add('file')
                                ->add('src', 'hidden')
                                ->add('data', 'hidden')->getForm();
                $eventPictureForms['event_' . $nextEvent->getId()] = $eventPictureForm->createView();
            }
        }

        $nextEventsForView = array();
        foreach ($nextEventsByMonth as $month => $nextEvents) {
            $nextEventsForView[$month] = array();
            foreach ($nextEvents as $nextEventByDate) {
                $localEvent = new \stdClass();
                $localEvent->event = $nextEventByDate;
                $localEvent->accepted = in_array($nextEventByDate, $eventsAccepted->toArray());
                $nextEventsForView[$month][] = $localEvent;
            }
        }


        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array('pastEvent' => false, 'nextEvents' => $nextEventsForView,
                    'eventPictureForms' => $eventPictureForms));
    }

    public function eventsPastPanelAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $eventsAccepted = $user->getParticipateEvents();
        $pastEventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findPastEventOrderedByDate();

        $eventPictureForms = array();
        foreach ($pastEventsByMonth as $month => $pastEvents) {
            foreach ($pastEvents as $pastEvent) {
                $eventPicture = new EventPicture();
                $formFactory = $this->container->get('form.factory');

                $eventPictureFormBuilder = $formFactory->createBuilder('form', $eventPicture);
                $eventPictureForm = $eventPictureFormBuilder->add('file')
                                ->add('src', 'hidden')
                                ->add('data', 'hidden')->getForm();
                $eventPictureForms['event_' . $pastEvent->getId()] = $eventPictureForm->createView();
            }
        }

        $pastEventsForView = array();
        foreach ($pastEventsByMonth as $month => $pastEvents) {
            $nextEventsForView[$month] = array();
            foreach ($pastEvents as $pastEventByDate) {
                $localEvent = new \stdClass();
                $localEvent->event = $pastEventByDate;
                $localEvent->accepted = in_array($pastEventByDate, $eventsAccepted->toArray());
                $pastEventsForView[$month][] = $localEvent;
            }
        }


        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array('pastEvent' => true, 'nextEvents' => $pastEventsForView,
                    'eventPictureForms' => $eventPictureForms));
    }

    /**
     * @Template()
     */
    public function eventCreateAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $event = new Event($user);
        $form = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('admin_create_event'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_event'));
        }
        return array(
            'event' => $event,
            'eventForm' => $form->createView(),
        );
    }

    public function eventPictureUploadAction(Request $request, $eventId) {
        $eventPicture = new EventPicture();
        $form = $this->createFormBuilder($eventPicture, array('csrf_protection' => false))
                ->add('file')
                ->add('src', 'hidden')
                ->add('data', 'hidden')
                ->getForm();
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->find($eventId);
        $retour = $this->generateUrl('admin_panel_event');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $em->persist($eventPicture);
                $em->flush();

                $event->setPicture($eventPicture);
                $em->flush();

                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => $eventPicture->getAjaxMsg(),
                            'result' => $eventPicture->getResult()
                )));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        return $this->redirect($retour);
    }

    public function eventEditAction(Request $request, $eventId) {

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->find($eventId);
        $form = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('admin_create_event'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_event'));
        }

        return $this->render('OmaracujaAdminBundle:Admin:eventCreate.html.twig', array(
                    'event' => $event,
                    'eventForm' => $form->createView(),
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
    
    public function picturePanelAction(Request $request) {
        $picture = new Picture();
        $form = $this->createFormBuilder($picture, array('csrf_protection' => false))
                ->add('file')
                ->add('src', 'hidden')
                ->add('data', 'hidden')
                ->getForm();
        $em = $this->getDoctrine()->getManager();
        $pictures = $em->getRepository('OmaracujaFrontBundle:Picture')->findAll();
        $retour = $this->generateUrl('admin_panel_pictures');
//        if ($request->isMethod('POST')) {
//            $form->bind($request);
//            if ($form->isValid()) {
//
//                $em->persist($picture);
//                $em->flush();
//
//                $response = new Response(json_encode(array(
//                            'state' => 200,
//                            'message' => $picture->getAjaxMsg(),
//                            'result' => $picture->getResult()
//                )));
//                $response->headers->set('Content-Type', 'application/json');
//                return $response;
//            }
//        }
        return $this->render('OmaracujaAdminBundle:Admin:picturePanel.html.twig', array(
                    'pictures' => $pictures,
                    'form' => $form->createView(),
        ));
    }
}
