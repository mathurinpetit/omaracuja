<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\RegistrationController as FOSRegistrationController;


class RegistrationController extends FOSRegistrationController {

    public function registerAction(Request $request) {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(false);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->add('name', 'text', array('label' => 'Nom :'));
        $form->add('firstname', 'text', array('label' => 'Prénom :'));

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

               // $userManager->updateUser($user);

                $this->sendRegistrationMailToUser($user,$request);
                $this->sendRegistrationMailToAdmins($user);
                
//                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
//                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('OmaracujaUserBundle:Registration:register.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    private function sendRegistrationMailToAdmins($user) {
        
    }
    
    private function sendRegistrationMailToUser($user,$request) {
        
        $senderEmail = $this->container->getParameter('senderEmail');
        $message = \Swift_Message::newInstance();
        $message->setReturnPath($senderEmail);
        $message->setReplyTo($senderEmail);
        $message->setFrom($senderEmail);
        
        $subject = $user->getUsername().", bienvenue sur le site Omaracuja.com";
        
        $webPath = $this->get('kernel')->getRootDir().'/../web';
        $path_logo = $message->embed(\Swift_Image::fromPath($webPath."/omaracuja_logo.png"));
        
        $mailBody = $this->container->get('templating')->render('OmaracujaUserBundle:Registration:registrationMail.html.twig', array('user' => $user, 'url_logo' => $path_logo));
        
        $message->setSubject($subject);
        $message->setTo($user->getEmail());
        $message->setBody($mailBody, 'text/html');
        $this->get('mailer')->send($message);
    }

}
