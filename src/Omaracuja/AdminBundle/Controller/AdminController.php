<?php

namespace Omaracuja\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Omaracuja\FrontBundle\Entity\Video as Video;
use Omaracuja\FrontBundle\Entity\Event as Event;
use Omaracuja\FrontBundle\Entity\EventPicture as EventPicture;
use Omaracuja\FrontBundle\Entity\Picture as Picture;
use Omaracuja\FrontBundle\Entity\NewsLetterMember as NewsLetterMember;
use Omaracuja\FrontBundle\Form\VideoType as VideoType;
use Omaracuja\FrontBundle\Form\EventType as EventType;
use Omaracuja\FrontBundle\Form\EventAlbumType as EventAlbumType;
use Omaracuja\EmailManagerBundle\lib\EmailManager as EmailManager;

class AdminController extends Controller {

    /**
     * @Template()
     */
    public function userPanelAction(Request $request) {
        $selectedUser = ( $request->get('selectedUser') >= 0 ) ? $request->get('selectedUser') : null;
        $em = $this->getDoctrine()->getManager();
        $usersAdmin = $em->getRepository('OmaracujaUserBundle:User')->findByRole('ROLE_ADMIN');
        $usersNoAdmin = $em->getRepository('OmaracujaUserBundle:User')->findByHasNotRole('ROLE_ADMIN');
        return array('usersAdmin' => $usersAdmin, 'usersNoAdmin' => $usersNoAdmin, 'selectedUser' => $selectedUser);
    }

    /**
     * @Template()
     */
    public function videosAction() {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('OmaracujaFrontBundle:Video')->findAllOrderedByDate();
        return $this->render('OmaracujaAdminBundle:Admin:videosPanel.html.twig', array('videos' => $videos));
    }

    /**
     * @Template()
     */
    public function videoCreateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('OmaracujaFrontBundle:Video')->findAllOrderedByDate();
        $video = new Video();
        $form = $this->createForm(new VideoType(), $video, array(
            'action' => $this->generateUrl('admin_panel_videos_add'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_video'));
        }
        return array(
            'video' => $video,
            'videoForm' => $form->createView(),
        );
    }

    public function videoDeleteAction($idVideo, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $video = $em->getRepository('OmaracujaFrontBundle:Video')->find($idVideo);

        if (!$video) {
            throw $this->createNotFoundException("La vidéo n'existe pas en base de donnée!");
        }

        $em->remove($video);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel_video'));
    }

    /**
     * @Template()
     */
    public function eventPanelAction() {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $eventsAccepted = $user->getParticipateEvents();
        $nextEventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate();

        $eventPictureForms = $this->getEventsPicturesForms($nextEventsByMonth);

        $nextEventsForView = $this->getEventsForView($nextEventsByMonth, $eventsAccepted);


        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array('pastEvent' => false, 'nextEvents' => $nextEventsForView,
                    'eventPictureForms' => $eventPictureForms));
    }

    public function eventsPastPanelAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $eventsAccepted = $user->getParticipateEvents();
        $pastEventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findPastEventOrderedByDate();

        $eventPictureForms = $this->getEventsPicturesForms($pastEventsByMonth);

        $pastEventsForView = $this->getEventsForView($pastEventsByMonth, $eventsAccepted);

        return $this->render('OmaracujaAdminBundle:Admin:eventPanel.html.twig', array('pastEvent' => true, 'nextEvents' => $pastEventsForView,
                    'eventPictureForms' => $eventPictureForms));
    }

    /**
     * @Template()
     */
    public function eventCreateAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $event = new Event($user);
        $form = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('admin_create_event'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->sendMailToUsersAndAdminsNewEvent($event);

            return $this->redirect($this->generateUrl('admin_panel_event'));
        }
        return array(
            'event' => $event,
            'eventForm' => $form->createView(),
        );
    }

    public function eventPictureUploadAction(Request $request, $eventId) {
        $eventPicture = new EventPicture();
        $form = $this->createFormBuilder($eventPicture, array('csrf_protection' => false))
                ->add('file')
                ->add('src', 'hidden')
                ->add('data', 'hidden')
                ->getForm();
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->find($eventId);
        $retour = $this->generateUrl('admin_panel_event');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $em->persist($eventPicture);
                $em->flush();

                $event->setPicture($eventPicture);
                $em->flush();

                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => $eventPicture->getAjaxMsg(),
                            'result' => $eventPicture->getResult()
                )));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        return $this->redirect($retour);
    }

    public function eventEditAction(Request $request, $eventId) {

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->find($eventId);
        $form = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('admin_event_edit', array('eventId' => $eventId)),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_panel_event'));
        }

        return $this->render('OmaracujaAdminBundle:Admin:eventCreate.html.twig', array(
                    'event' => $event,
                    'eventForm' => $form->createView(),
        ));
    }

    public function userActivateAction($userId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->activate();
        $em->persist($entity);
        $em->flush();
        $this->sendActivationUserMail($entity);
        return $this->redirect($this->generateUrl('admin_panel_users'));
    }

    public function userDesactivateAction($userId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OmaracujaUserBundle:User')->findOneById($userId);
        $entity->desactivate();
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_panel_users'));
    }

    public function addAlbumEventAction(Request $request, $eventId) {

        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('OmaracujaFrontBundle:Event')->find($eventId);
        $album = $em->getRepository('OmaracujaFrontBundle:Event')->findAlbumEventOrCreate($event);

        $em->persist($album);
        $em->flush();

        $event->setAlbum($album);
        $em->persist($event);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_edit_event_album', array('albumId' => $album->getId())));
    }

    public function editAlbumEventAction(Request $request, $albumId) {

        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('OmaracujaFrontBundle:EventAlbum')->find($albumId);
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->findOneByAlbum($album);
        $newPicture = new Picture($album);
        $newPictureform = $this->createPictureUploadForm($newPicture);

        $albumForm = $this->createForm(new EventAlbumType(), $album, array(
            'action' => $this->generateUrl('admin_edit_event_album', array('albumId' => $albumId)),
            'method' => 'POST',
            'csrf_protection' => false
        ));
        $albumForm->handleRequest($request);

        if ($albumForm->isValid()) {

            return $this->redirect($this->generateUrl('admin_edit_event_album', array('albumId' => $album->getId())));
        }
        return $this->render('OmaracujaAdminBundle:Admin:albumEdit.html.twig', array('album' => $album,
                    'newPicture' => $newPicture,
                    'event' => $event,
                    'albumForm' => $albumForm->createView(),
                    'newPictureform' => $newPictureform->createView()));
    }

    public function albumsPanelAction($mois) {

        $em = $this->getDoctrine()->getManager();
        $eventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findAllWithAlbumOrderedByDate();


        $albumForView = array();

        $last_month = null;
        $last_month_label = null;
        $next_month = null;
        $next_month_label = null;

        if (($mois == "now") || !preg_match('/^[0-9]{4}-[0-9]{2}$/', $mois)) {
            $mois = date('Y-m');
        }

        $eventsByMonthWithAlbum = array();
        foreach ($eventsByMonth as $month => $events) {
            foreach ($events as $event) {
                if ($event->getStartAt()->format("Ym") != str_replace('-', '', $mois)) {
                    if (!$last_month && ($event->getStartAt()->format("Ym") < str_replace('-', '', $mois))) {
                        $last_month = $event->getStartAt()->format("Y-m");
                        $last_month_label = $month;
                        continue;
                    }
                    if ($event->getCreatedAt()->format("Ym") > str_replace('-', '', $mois)) {
                        $next_month = $event->getStartAt()->format("Y-m");
                        $next_month_label = $month;
                        continue;
                    }
                    continue;
                }
                if (!array_key_exists($month, $eventsByMonthWithAlbum)) {
                    $eventsByMonthWithAlbum[$month] = array();
                }
                $eventWithAlbum = new \stdClass();
                $eventWithAlbum->event = $event;
                $eventWithAlbum->pictures = $em->getRepository('OmaracujaFrontBundle:Picture')->findByAlbum($event->getAlbum());
                $eventsByMonthWithAlbum[$month][] = $eventWithAlbum;
            }
        }
        return $this->render('OmaracujaAdminBundle:Admin:albumsPanel.html.twig', array(
                    'admin' => true,
                    'eventsByMonthWithAlbum' => $eventsByMonthWithAlbum,
                    'last_month' => $last_month,
                    'last_month_label' => $last_month_label,
                    'next_month' => $next_month,
                    'next_month_label' => $next_month_label
        ));
    }

    public function pictureUploadAction(Request $request, $albumId) {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('OmaracujaFrontBundle:EventAlbum')->find($albumId);
        $picture = new Picture($album);
        $form = $this->createPictureUploadForm($picture);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $errors = $this->get('validator')->validate($form);
            $errorsArray = array();
            foreach ($errors as $error) {
                $errorsArray[] = array(
                    'elementId' => str_replace('data.', '', $error->getPropertyPath()),
                    'errorMessage' => $error->getMessage(),
                );
            }
            if ($form->isValid()) {

                $em->persist($picture);
                $em->flush();
                $album->addPicture($picture);
                $em->flush();

                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => $picture->getAjaxMsg(),
                            'result' => $picture->getResult()
                )));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                $errorMessage = $errorsArray[0]['errorMessage'];
                $response = new Response(json_encode(array(
                            'state' => 200,
                            'message' => $errorMessage,
                            'result' => $picture->getResult()
                )));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        return $this->redirect($this->generateUrl('admin_panel_pictures'));
    }

    /*
     * NEWSLETTER
     */

    public function newsletterCreatePanelAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $newslettermembers = $em->getRepository('OmaracujaFrontBundle:NewsLetterMember')->findAll();

        $form = $this->createNewsLetterForm();
        if ($request->isMethod('POST')) {
             $form->bind($request);
            $datas = $form->getData();
            $mailBoby = htmlentities($datas["mailBody"]);
            $this->sendNewsLetter($mailBoby,$newslettermembers);
            return $this->redirect($this->generateUrl('admin_panel_newsletter'));
        }
        return $this->render('OmaracujaAdminBundle:Admin:newsLetterPanel.html.twig', array(
                    'newsLetterForm' => $form->createView()
        ));
    }

    private function createPictureUploadForm($picture) {
        return $this->createFormBuilder($picture, array('csrf_protection' => false))
                        ->add('file')
                        ->add('src', 'hidden')
                        ->add('data', 'hidden')
                        ->add('title', 'text')
                        ->add('description', 'text')
                        ->getForm();
    }

    private function createNewsLetterForm() {
        return $this->createFormBuilder()
                        ->add('mailBody', 'textarea', array(
                            'attr' => array(
                                'class' => 'summernote')))
                        ->getForm();
    }

    private function getEventsPicturesForms($eventsByMonth) {
        $eventPictureForms = array();
        foreach ($eventsByMonth as $month => $events) {
            foreach ($events as $event) {
                $eventPicture = new EventPicture();
                $formFactory = $this->container->get('form.factory');

                $eventPictureFormBuilder = $formFactory->createBuilder('form', $eventPicture);
                $eventPictureForm = $eventPictureFormBuilder->add('file')
                                ->add('src', 'hidden')
                                ->add('data', 'hidden')->getForm();
                $eventPictureForms['event_' . $event->getId()] = $eventPictureForm->createView();
            }
        }
        return $eventPictureForms;
    }

    private function getEventsForView($eventsByMonth, $eventsAccepted) {
        $eventsForView = array();
        foreach ($eventsByMonth as $month => $events) {
            $eventsForView[$month] = array();
            foreach ($events as $eventByDate) {
                $localEvent = new \stdClass();
                $localEvent->event = $eventByDate;
                $localEvent->accepted = in_array($eventByDate, $eventsAccepted->toArray());
                $eventsForView[$month][] = $localEvent;
            }
        }
        return $eventsForView;
    }

    private function sendActivationUserMail($user) {

        $senderEmail = $this->container->getParameter('senderEmail');
        $message = \Swift_Message::newInstance();
        $message->setReturnPath($senderEmail);
        $message->setReplyTo($senderEmail);
        $message->setFrom($senderEmail);

        $subject = $user->getUsername() . ", votre compte est validé sur le site Omaracuja.com";


        $mailBody = $this->render('OmaracujaAdminBundle:Admin:activationUserMail.html.twig', array('user' => $user));

        $message->setSubject($subject);
        $message->setTo($user->getEmail());
        $message->setBody($mailBody, 'text/html');
        $this->get('mailer')->send($message);
    }

    private function sendMailToUsersAndAdminsNewEvent($event) {

        $proposedUsers = array();
        $proposedAdmins = array();

        foreach ($event->getProposedTeam() as $proposedUser) {
            if (!$proposedUser->isAdmin()) {
                $proposedUsers[] = $proposedUser;
            } else {
                $proposedAdmins[] = $proposedUser;
            }
        }

        $emailManager = new EmailManager($this->get('mailer'), $this->get('templating'), $this->container->getParameter('senderEmail'));

        $emailManager->sendMailToUsersNewEvent($proposedUsers, $event);
        $emailManager->sendMailToAdminsNewEvent($proposedAdmins, $event);
    }

    private function sendNewsLetter($mailBoby,$newslettermembers) {
        $emailManager = new EmailManager($this->get('mailer'), $this->get('templating'), $this->container->getParameter('senderEmail'));

        $emailManager->sendNewsletter($mailBoby,$newslettermembers);
    }

}
