<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as FOSSecurityController;

class ProfileController extends FOSSecurityController {    
    
    protected function renderLogin(array $data)
    {
        return $this->render('OmaracujaUserBundle:Security:connexion.html.twig', $data);
    }
}
