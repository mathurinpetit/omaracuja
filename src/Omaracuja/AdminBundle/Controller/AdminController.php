<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;
use Omaracuja\AdminBundle\Form\PresentationType as PresentationType;
use Omaracuja\FrontBundle\Entity\Event as Event;
use Omaracuja\FrontBundle\Entity\EventPicture as EventPicture;
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
        $em = $this->getDoctrine()->getManager();
        $lastEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findAllOrderedByDate();
        $eventPictureForms = array();
        foreach ($lastEvents as $lastEvent) {
            $eventPicture = new EventPicture();
            $formFactory = $this->container->get('form.factory');

            $eventPictureFormBuilder = $formFactory->createBuilder('form', $eventPicture);
            $eventPictureForm = $eventPictureFormBuilder->add('file')
                            ->add('src', 'hidden')
                            ->add('data', 'hidden')->getForm();
            $eventPictureForms['event_' . $lastEvent->getId()] = $eventPictureForm->createView();
        }
        
        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array('lastEvents' => $lastEvents,
            'eventPictureForms' => $eventPictureForms));
    }

    /**
     * @Template()
     */
    public function eventCreateAction(Request $request) {
        $event = new Event();
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

    public function eventPictureUploadAction(Request $request) {
        $event = new Event();

        $form = $this->createFormBuilder($event, array('csrf_protection' => false))
                ->add('file')
                ->add('src', 'hidden')
                ->add('data', 'hidden')
                ->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $em->persist($event);
                $em->flush();

                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => $event->getAjaxMsg(),
                            'result' => $event->getResult()
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
        $eventForm = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('admin_event_create'),
            'method' => 'POST',
        ));

        $event->getPicture();

        $eventPictureForm = $this->createFormBuilder($event->getPicture(), array('csrf_protection' => false))
                ->add('file')
                ->add('src', 'hidden')
                ->add('data', 'hidden')
                ->getForm();
        $eventForm->handleRequest($request);

        if ($eventForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventForm);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_event'));
        }

        return $this->render('OmaracujaAdminBundle:Admin:eventEdit.html.twig', array(
                    'event' => $event,
                    'eventForm' => $eventForm->createView(),
                    'eventPictureForm' => $eventPictureForm->createView()
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
