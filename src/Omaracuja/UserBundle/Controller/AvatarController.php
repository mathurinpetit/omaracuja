<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Omaracuja\UserBundle\Entity\Avatar as Avatar;

class AvatarController extends Controller {

    public function uploadAction(Request $request) {
        $avatar = new Avatar();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $avatar->setUser($user);

        $form = $this->createFormBuilder($avatar, array('csrf_protection' => false))
                ->add('file')
                ->add('src', 'hidden')
                ->add('data', 'hidden')
                ->getForm();


        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $em->persist($avatar);
                $em->flush();

                $user->setSelectedAvatar($avatar);
                $em->persist($user);
                $em->flush();

                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => $avatar->getAjaxMsg(),
                            'result' => $avatar->getResult()
                )));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        return $this->redirect($retour);
    }

}
