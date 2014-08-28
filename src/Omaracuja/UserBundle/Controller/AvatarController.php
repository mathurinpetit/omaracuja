<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Omaracuja\UserBundle\Entity\Avatar as Avatar;

class AvatarController extends Controller {

    public function uploadAction(Request $request) {
        $avatar = new Avatar();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $avatar->setUser($user);
        $form = $this->createFormBuilder($avatar,array('csrf_protection' => false))
                ->add('file')->getForm();
        
        $retour = $this->generateUrl('fos_user_profile_edit');
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();          

                $em->persist($avatar);
                $em->flush();
                
                $user->setSelectedAvatar($avatar);
                $em->persist($user);
                $em->flush();
                $retour = $this->generateUrl('avatar_upload_resize',array('id' => $avatar->getId()));
                return $this->redirect($retour);
            }
        }
        return $this->redirect($retour);
    }
    
    public function resizeAction($id) {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OmaracujaUserBundle:Avatar')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Avatar entity.');
        }
        
        return $this->render('OmaracujaUserBundle:Profile:avatar_resize.html.twig', array(
            'avatar' => $entity,
        ));
    }

}
