<?php

namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Omaracuja\FrontBundle\Entity\NewsLetterMember as NewsLetterMember;
use Omaracuja\FrontBundle\Form\NewsLetterMemberType as NewsLetterMemberType;

class FrontController extends Controller {

    /**
     * @Template()
     */
    public function bulleMaracujaAction() {
        return array();
    }

    /**
     * @Template()
     */
    public function contactAction() {
        $em = $this->getDoctrine()->getManager();
        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);
        return array('responsable' => $this->container->getParameter('responsable'),
            'siegeSocial' => $this->container->getParameter('siegeSocial'), 'events' => $nextEvents);
    }

    /**
     * @Template()
     */
    public function videosAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);
        $videos = $em->getRepository('OmaracujaFrontBundle:Video')->findAllOrderedByDate();
        return array('videos' => $videos, 'events' => $nextEvents);
    }

    /**
     * @Template()
     */
    public function evennementAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);

        return array(
            'pastEvent' => false,
            'events' => $nextEvents,
            'currentSelectedEventId' => $id
        );
    }

    public function nextEvennementAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);
        $num_lien = 1;
        $nextEventsJson = array();
        $nextEventsJson[] = array("lien" => "", "imgsrc" => "", "date" => "", "lieu" => "", "titre" => "", "texte" => "", "x" => "", "y" => "", "zoom" => "");
        foreach ($nextEvents as $month => $eventByMonth) {
            foreach ($eventByMonth as $event) {

                $eventJson = array("lien" => $num_lien,
                    "imgsrc" => $event->getPicturePath(),
                    "date" => $event->getDateAAfficher(),
                    "lieu" => $event->getLieuAAfficher(),
                    "titre" => $event->getTitle(),
                    "texte" => $event->getPublicDescription(),
                    "x" => $event->getMapX(),
                    "y" => $event->getMapY(),
                    "zoom" => 15
                );
                $num_lien++;
                $nextEventsJson[] = $eventJson;
            }
        }

        $response = new Response();
        $response->setContent(json_encode($nextEventsJson));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function photosViewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $randomPictures = $em->getRepository('OmaracujaFrontBundle:Picture')->find10RandomPictures();
        $randomPicturesJson = array();
        $num_picture = 1;
        foreach ($randomPictures as $picture) {
            $randomPicturesJson[] = array("numero" => $num_picture,
                "imgsrc" => $picture->getCurrentPicturePath(),
                "titre" => $picture->getTitle(),
                "texte" => $picture->getDescription()
            );
            $num_picture++;
        }
        $response = new Response();
        $response->setContent(json_encode($randomPicturesJson));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function videosViewAction(Request $request) {
        $videosJson = array();
        $nbVideos = rand(1,4);
        for ($index = $nbVideos; $index <= $nbVideos; $index++) {
            $videosJson[] = array("numero" => $index,
                "vidsrc" => "videos/omaracuja_video_$index.mp4",
                "preview" => "videos/omaracuja_video_$index.jpg",
                "titre" => "Omaracuja $index",
                "texte" => "Omaracuja description $index"
            );
        }
        $response = new Response();
        $response->setContent(json_encode($videosJson));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function evennementPastAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findPastEventOrderedByDate(true);

        return $this->render('OmaracujaFrontBundle:Front:evennement.html.twig', array(
                    'pastEvent' => true,
                    'events' => $nextEvents,
                    'currentSelectedEventId' => null
        ));
    }

    /**
     * @Template()
     */
    public function presentationAction() {
        $contenu_presentation = $this->container->getParameter('pagePresentation');
        $numeroVideo = rand(1,4);
        $em = $this->getDoctrine()->getManager();

        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);

        return array('contenu_presentation' => $contenu_presentation,
            'numeroVideo' => $numeroVideo,
            'events' => $nextEvents);
    }

    /**
     * @Template()
     */
    public function newsletterAction(Request $request) {
         $em = $this->getDoctrine()->getManager();
         $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findPastEventOrderedByDate(true);
        $newsletterMember = new NewsLetterMember();
        $form = $this->createForm(new NewsLetterMemberType(), $newsletterMember, array(
            'action' => $this->generateUrl('front_newsletter'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($newsletterMember);
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                return array('form' => $form->createView(), 'erreur' => true);
            }
            return $this->redirect($this->generateUrl('front_presentation'));
        }
        return array('form' => $form->createView(), 'events' => $nextEvents);
    }

    public function albumsAction($mois) {
        $em = $this->getDoctrine()->getManager();
        $eventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findAllWithAlbumOrderedByDate();
        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);
        if (($mois == "now") || !preg_match('/^[0-9]{4}-[0-9]{2}$/', $mois)) {
            $mois = date('Y-m');
        }

        $eventsByMonthWithAlbum = array();
        foreach ($eventsByMonth as $month => $events) {
            foreach ($events as $event) {
                if (!array_key_exists($month, $eventsByMonthWithAlbum)) {
                    $eventsByMonthWithAlbum[$month] = array();
                }
                $eventWithAlbum = new \stdClass();
                $eventWithAlbum->event = $event;
                $eventWithAlbum->pictures = $em->getRepository('OmaracujaFrontBundle:Picture')->findByAlbum($event->getAlbum());
                $eventsByMonthWithAlbum[$month][] = $eventWithAlbum;
            }
        }

        return $this->render('OmaracujaFrontBundle:Front:albums.html.twig', array(
                    'eventsByMonthWithAlbum' => $eventsByMonthWithAlbum, 'events' => $nextEvents
        ));
    }

    public function albumAction($albumId) {
        $em = $this->getDoctrine()->getManager();
         $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true, true);
        $album = $em->getRepository('OmaracujaFrontBundle:EventAlbum')->find($albumId);
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->findOneByAlbum($album);

        return $this->render('OmaracujaFrontBundle:Front:album.html.twig', array('album' => $album,
                    'event' => $event,'events' => $nextEvents));
    }

    public function downloadPictureAction($idPicture) {
        $em = $this->getDoctrine()->getManager();
        $picture = $em->getRepository('OmaracujaFrontBundle:Picture')->find($idPicture);
        $path = $this->get('kernel')->getRootDir() . "/../web";
        $content = file_get_contents($path . $picture->getCurrentPicturePath());

        $response = new Response();
        $ext = strstr($picture->getCurrentPicturePath(), '.');

        $response->headers->set('Content-Type', mime_content_type($path . $picture->getCurrentPicturePath()));
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $picture->getTitle() . $ext);

        $response->setContent($content);
        return $response;
    }

}
