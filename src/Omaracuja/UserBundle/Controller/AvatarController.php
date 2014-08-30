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

        $retour = $this->generateUrl('fos_user_profile_edit');
        
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
    
     public function selectAction(Request $request) {
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        $avatars = $user->getAvatars();

        $avatarChooseForm = $this->createFormBuilder($user, array('csrf_protection' => false))
                ->add('selectedAvatar')->getForm();
        $retour = $this->generateUrl('fos_user_profile_edit');

        if ($request->isMethod('POST')) {
            $avatarChooseForm->bind($request);
            if ($avatarChooseForm->isValid()) {
                
                $em = $this->getDoctrine()->getManager();
                $user = $avatarChooseForm->getData();
                $em->persist($user);
                $em->flush();
                
                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => "Enregistré",
                            'result' => $user->getCurrentAvatarPath()
                )));
                return $this->redirect($retour);
            }
        }
    }

}
