<?php

namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller {

    /**
     * @Template()
     */
    public function contactAction() {
        return array('responsable' => $this->container->getParameter('responsable'),
            'siegeSocial' => $this->container->getParameter('siegeSocial'));
    }

    /**
     * @Template()
     */
    public function videosAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('OmaracujaFrontBundle:Video')->findAllOrderedByDate();
        return array('videos' => $videos);
    }

    /**
     * @Template()
     */
    public function evennementAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true);
        return array(
            'pastEvent' => false,
            'events' => $nextEvents,
        );
    }

    public function evennementPastAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findPastEventOrderedByDate(true);

        return $this->render('OmaracujaFrontBundle:Front:evennement.html.twig', array(
                    'pastEvent' => true,
                    'events' => $nextEvents,
        ));
    }

    /**
     * @Template()
     */
    public function presentationAction() {
        $contenu_presentation = $this->container->getParameter('pagePresentation');
        $img_path_random = "";
        $em = $this->getDoctrine()->getManager();

        $nextEvents = $em->getRepository('OmaracujaFrontBundle:Event')->findNextOrderedByDate(true);
       
        return array('contenu_presentation' => $contenu_presentation,
                     'img_path_random' => $img_path_random,
                     'events' => $nextEvents);
    }

    public function albumsAction($mois) {
        $em = $this->getDoctrine()->getManager();
        $eventsByMonth = $em->getRepository('OmaracujaFrontBundle:Event')->findAllWithAlbumOrderedByDate();
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
        
        return $this->render('OmaracujaFrontBundle:Front:albums.html.twig', array(
                    'eventsByMonthWithAlbum' => $eventsByMonthWithAlbum,
                    'last_month' => $last_month,
                    'last_month_label' => $last_month_label,
                    'next_month' => $next_month,
                    'next_month_label' => $next_month_label
        ));
    }

    public function albumAction($albumId) {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('OmaracujaFrontBundle:EventAlbum')->find($albumId);
        $event = $em->getRepository('OmaracujaFrontBundle:Event')->findOneByAlbum($album);

        return $this->render('OmaracujaFrontBundle:Front:album.html.twig', array('album' => $album,
                    'event' => $event));
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
