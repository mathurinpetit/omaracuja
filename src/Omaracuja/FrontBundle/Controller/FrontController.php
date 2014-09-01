<?php

namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FrontController extends Controller {

    /**
     * @Template()
     */
    public function accueilAction() {
        return array();
    }
        /**
     * @Template()
     */
    public function presentationAction() {
        return array();
    }
    

}
