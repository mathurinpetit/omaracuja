<?php
namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ConstructionController extends Controller
{
    /**
     * @Route("/construction")
     * @Template()
     */
    public function constructionAction()
    {
        return array();
    }
}
