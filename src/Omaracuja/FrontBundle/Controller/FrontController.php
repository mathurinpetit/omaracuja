<?php

namespace Omaracuja\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Omaracuja\AdminBundle\Entity\Presentation as Presentation;
use Omaracuja\FrontBundle\Entity\BlogPost;
use Omaracuja\FrontBundle\Form\BlogPostType;

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
        $blogPosts = $this->getBlogPostsStrategy();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $connected = !($user == "anon.") && $user->isActif();

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

    private function getBlogPostsStrategy(){
        $em = $this->getDoctrine()->getManager();
       return $em->getRepository('OmaracujaFrontBundle:BlogPost')->findAll();
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

            return $this->redirect($this->generateUrl('front_accueil'));
        }

        return $this->render('OmaracujaFrontBundle:BlogPost:new.html.twig', array(
                    'entity' => $newBlogPost,
                    'form' => $newBlogPostForm->createView(),
        ));
    }


}
