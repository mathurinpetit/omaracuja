<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller
{
    /**
     * @Template()
     */
    public function userPanelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OmaracujaUserBundle:User')->findAll();
        return array('users' => $entities);
    }
    
    /**
     * @Template()
     */
    public function presentationPanelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OmaracujaUserBundle:User')->findAll();
        return array('users' => $entities);
    }
    
    /**
     * @Template()
     */
    public function eventPanelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OmaracujaUserBundle:User')->findAll();
        return array('users' => $entities);
    }
    
    
    
    
    

    public function userActivateAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->activate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel'));
    }
    

    public function userDesactivateAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->desactivate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel'));
    }
}
