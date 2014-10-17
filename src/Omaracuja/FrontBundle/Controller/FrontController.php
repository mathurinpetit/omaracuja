<?php

namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;
use Omaracuja\FrontBundle\Entity\BlogPost;
use Omaracuja\FrontBundle\Form\BlogPostType;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller {

    /**
     * @Template()
     */
    public function accueilAction(Request $request) {
        return $this->getAccueil();
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
        $em = $this->getDoctrine()->getManager();
        $presentations = $em->getRepository('OmaracujaAdminBundle:Presentation')->findAll();
        $actual_presentation = null;
        foreach ($presentations as $presentation) {
            if ($presentation->isSelected()) {
                $actual_presentation = $presentation;
            }
        }
        return array('actual_presentation' => $actual_presentation);
    }

    private function getAccueil() {
        //User        
        $user = $this->container->get('security.context')->getToken()->getUser();
        $connected = !($user == "anon.") && $user->isActif();
        $blogPosts = $this->getBlogPostsStrategy($connected, $user);

        //PostCreation
        $newBlogPost = null;
        $newBlogPostForm = null;
        if ($connected) {
            $newBlogPost = new BlogPost($user);
            $newBlogPostForm = $this->createForm(new BlogPostType(), $newBlogPost, array(
                'action' => $this->generateUrl('front_blog_post_create'),
                'method' => 'POST',
            ));

            return $this->render('OmaracujaFrontBundle:Front:accueil.html.twig', array('blogPosts' => $blogPosts, 'user' => $user, 'connected' => $connected, 'newBlogPostForm' => $newBlogPostForm->createView()));
        }
        return $this->render('OmaracujaFrontBundle:Front:accueil.html.twig', array('blogPosts' => $blogPosts, 'user' => $user, 'connected' => $connected));
    }

    private function getBlogPostsStrategy($connected = false, $user = false) {
        $em = $this->getDoctrine()->getManager();
        $blogPostRepository = $em->getRepository('OmaracujaFrontBundle:BlogPost');
        if ($connected && $user->isActif()) {
            return $blogPostRepository->findAllOrderedByDateWithLimit();
        } else {
            return $blogPostRepository->findPublicOrderedByDateWithLimit();
        }
    }

    public function blogPostCreateAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $newBlogPost = new BlogPost($user);
        $newBlogPostForm = $this->createForm(new BlogPostType(), $newBlogPost, array(
            'action' => $this->generateUrl('front_blog_post_create'),
            'method' => 'POST',
        ));

        $newBlogPostForm->handleRequest($request);

        if ($newBlogPostForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newBlogPost);
            $em->flush();

            return $this->redirect($this->generateUrl('front_presentation'));
        }

        return $this->render('OmaracujaFrontBundle:BlogPost:new.html.twig', array(
                    'entity' => $newBlogPost,
                    'form' => $newBlogPostForm->createView(),
        ));
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
