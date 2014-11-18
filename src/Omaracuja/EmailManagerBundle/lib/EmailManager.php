<?php

namespace Omaracuja\EmailManagerBundle\lib;

use Symfony\Component\DependencyInjection\ContainerAware;

class EmailManager extends ContainerAware {

    public function sendRegistrationMailToAdmins($user) {

        $senderEmail = $this->container->getParameter('senderEmail');
        $message = \Swift_Message::newInstance();
        $message->setReturnPath($senderEmail);
        $message->setReplyTo($senderEmail);
        $message->setFrom($senderEmail);

        $subject = $user->getUsername() . " vient de s'inscrire sur le site Omaracuja.com";

        $userManager = $this->container->get('fos_user.user_manager');
        $all_users = $userManager->findUsers();

        $admins = array();
        foreach ($all_users as $user_local) {
            if ($user_local->isAdmin()) {
                $admins[] = $user_local;
            }
        }

        foreach ($admins as $admin) {
            $mailBody = $this->container->get('templating')->render('OmaracujaEmailManagerBundle:Emails:notificationAdminRegistrationMail.html.twig', array('user' => $user, 'admin' => $admin));
            $message->setSubject($subject);
            $message->setTo($admin->getEmail());
            $message->setBody($mailBody, 'text/html');
            $this->get('mailer')->send($message);
        }
    }

    public function sendRegistrationMailToUser($user) {
        $senderEmail = $this->container->getParameter('senderEmail');
        $message = \Swift_Message::newInstance();
        $message->setReturnPath($senderEmail);
        $message->setReplyTo($senderEmail);
        $message->setFrom($senderEmail);

        $subject = $user->getUsername() . ", bienvenue sur le site Omaracuja.com";
        $mailBody = $this->container->get('templating')->render('OmaracujaEmailManagerBundle:Emails:registrationMail.html.twig', array('user' => $user));

        $message->setSubject($subject);
        $message->setTo($user->getEmail());
        $message->setBody($mailBody, 'text/html');
        $this->get('mailer')->send($message);
    }

}
