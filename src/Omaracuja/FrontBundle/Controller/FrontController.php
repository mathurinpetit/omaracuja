<?php

namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;

class FrontController extends Controller {

    /**
     * @Template()
     */
    public function accueilAction() {
        return $this->getAccueil();
    }

    /**
     * @Template()
     */
    public function presentationAction() {
        $em = $this->getDoctrine()->getManager();
        $presentations = $em->getRepository('OmaracujaAdminBundle:Presentation')->findAll();
        $actual_presentation = null;
        foreach ($presentations as $presentation) {
            if ($presentation->isSelected()) {
                $actual_presentation = $presentation;
            }
        }
        return array('actual_presentation' => $actual_presentation);
    }

    private function getAccueil() {
        $em = $this->getDoctrine()->getManager();
        $blogPosts = $em->getRepository('OmaracujaFrontBundle:BlogPost')->findAll();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $connected = !($user == "anon.");
        return $this->render('OmaracujaFrontBundle:Front:accueil.html.twig', array('blogPosts' => $blogPosts, 'user' => $user,'connected' => $connected
        ));
    }

}
