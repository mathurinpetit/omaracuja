<?php

namespace Omaracuja\EmailManagerBundle\lib;

use Symfony\Component\Templating\EngineInterface;

class EmailManager {

    protected $mailer;
    protected $templating;
    protected $from;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, $from) {

        $this->mailer = $mailer;

        $this->templating = $templating;

        $this->from = $from;
    }

    public function sendRegistrationMailToAdmins($user, $admins) {

        $template = 'OmaracujaEmailManagerBundle:Emails:notificationAdminRegistrationMail.html.twig';

        $subject = $user->getUsername() . " vient de s'inscrire sur le site Omaracuja.com";

        foreach ($admins as $admin) {

            $to = $admin->getEmail();

            $body = $this->templating->render($template, array('user' => $user, 'admin' => $admin));

            $this->sendMessage($this->from, $to, $subject, $body);
        }
    }

    public function sendRegistrationMailToUser($user) {

        $template = 'OmaracujaEmailManagerBundle:Emails:registrationMail.html.twig';

        $subject = $user->getUsername() . ", bienvenue sur le site Omaracuja.com";

        $to = $user->getEmail();

        $body = $this->templating->render($template, array('user' => $user));

        $this->sendMessage($this->from, $to, $subject, $body);
    }

    public function sendMailToUsersNewEvent($users,$event) {
        $template = 'OmaracujaEmailManagerBundle:Emails:newEventUserMail.html.twig';

        $subject = "[Omaracuja.com Evennement] ".$event->getTitle();

        foreach ($users as $user) {

            $to = $user->getEmail();

            $body = $this->templating->render($template, array('user' => $user, 'event' => $event));

            $this->sendMessage($this->from, $to, $subject, $body);
        }
    }

    public function sendMailToAdminsNewEvent($admins,$event) {
        $template = 'OmaracujaEmailManagerBundle:Emails:newEventAdminMail.html.twig';

        $subject = "[Omaracuja.com Evennement] ".$event->getTitle();

        foreach ($admins as $admin) {

            $to = $admin->getEmail();

            $body = $this->templating->render($template, array('admin' => $admin, 'event' => $event));

            $this->sendMessage($this->from, $to, $subject, $body);
        }
    }
    
    public function sendNewsletter($body,$members) {
        $template = 'OmaracujaEmailManagerBundle:Emails:newsletters.html.twig';
        $subject = "Omaracuja.com Newsletter";
        foreach ($members as $newslettermember) {

            $to = $newslettermember->getEmail();

            $body = $this->templating->render($template, array('body' => $body));

            $this->sendMessage($this->from, $to, $subject, $body);
        }
    }

    protected function sendMessage($from, $to, $subject, $body) {
        $mail = \Swift_Message::newInstance();
        $mail->setFrom($from)
                ->setReturnPath($from)
                ->setReplyTo($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($body)
                ->setContentType('text/html');
        $this->mailer->send($mail);
    }

}
