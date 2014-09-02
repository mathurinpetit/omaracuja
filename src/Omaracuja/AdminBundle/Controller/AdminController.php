<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;
use Omaracuja\AdminBundle\Form\PresentationType as PresentationType;

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
        $form->add('submit', 'submit', array('label' => 'Enregistrer'));

        return array(
            'presentation' => $presentation,
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     */
    public function presentationCreateAction(Request $request) {
        $entity = new Presentation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_presentation'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     */
    public function eventPanelAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OmaracujaUserBundle:User')->findAll();
        return array('users' => $entities);
    }

    public function userActivateAction($userId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->activate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel'));
    }

    public function userDesactivateAction($userId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->desactivate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel'));
    }

}
