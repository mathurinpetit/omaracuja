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
use FOS\UserBundle\Controller\ProfileController as FOSProfileController;
use Omaracuja\UserBundle\Entity\Avatar as Avatar;

class ProfileController extends FOSProfileController {

    /**
     * Edit the user
     */
    public function editAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $avatars = $user->getAvatars();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->container->get('fos_user.profile.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);
        $form->add('name', 'text', array('label' => 'Nom :'));
        $form->add('firstname', 'text', array('label' => 'Prénom :'));
        unset($form['current_password']);

        $this->initAvatarForms($user);
        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);
                $avatarFormResult = $request->get('form');
                if ($avatarFormResult['file']) {
                    $this->avatar->setFile($avatarFormResult['file']);
                    $this->avatar->upload();

                    $em = $this->container->get('doctrine')->getManager();
                    $em->persist($this->avatar);
                    $em->flush();
                }
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
                        'FOSUserBundle:Profile:edit.html.' . $this->container->getParameter('fos_user.template.engine'), array('form' => $form->createView(),
                    'avatarForm' => $this->avatarForm->createView(),
                    'avatar' => $this->avatar,
                    'avatarChooseForm' => $this->avatarChooseForm->createView(),
                    'user' => $user,
                    'avatars' => $avatars)
        );
    }

    public function showAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $avatars = $user->getAvatars();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $this->initAvatarForms($user);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Profile:show.html.' . $this->container->getParameter('fos_user.template.engine'), array('avatarForm' => $this->avatarForm->createView(),
                    'avatar' => $this->avatar,
                    'avatarChooseForm' => $this->avatarChooseForm->createView(),
                    'user' => $user,
                    'avatars' => $avatars));
    }

    private function initAvatarForms($user) {

        $this->avatar = new Avatar();
        $formFactory = $this->container->get('form.factory');

        $avatarFormBuilder = $formFactory->createBuilder('form', $this->avatar);
        $this->avatarForm = $avatarFormBuilder->add('file')
                        ->add('src', 'hidden')
                        ->add('data', 'hidden')->getForm();

        $this->avatarChooseForm = $formFactory->createBuilder('form', $user)->add('selectedAvatar')->getForm();
    }

}
