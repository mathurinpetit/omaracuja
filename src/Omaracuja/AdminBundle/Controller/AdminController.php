<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller
{
    /**
     * @Route("/test/indexPanel")
     * @Template()
     */
    public function indexPanelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('OmaracujaUserBundle:User')->findAll();
        return array('users' => $entities);
    }
}
