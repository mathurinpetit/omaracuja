<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\FrontBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Omaracuja\FrontBundle\Entity\EventAlbum;
use Omaracuja\FrontBundle\Entity\Event;
use Omaracuja\FrontBundle\Entity\Picture;
use Omaracuja\UserBundle\Entity\User;

class LoadEventData implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $this->loadEvents($manager);
        $this->loadEventAlbums($manager);
    }

    public function loadEvents($manager) {
        $admin = $manager->getRepository('OmaracujaUserBundle:User')->findOneByUsername("mathurin");
        $users = $manager->getRepository('OmaracujaUserBundle:User')->findAll();

        $eventsTitle = array("Fête du slip", "Fête du cassoulet", "Fête des Crabes", "Nuit des toilettes publiques");

        for ($annee = 2012; $annee <= 2015; $annee++) {
            for ($mois = 1; $mois <= 12; $mois++) {
                $nb_event = rand(0, 10);
                for ($i = 0; $i <= $nb_event; $i++) {
                    $jour = rand(1, 26);
                    $date_debut = new \DateTime('' . $annee . '-' . $mois . '-' . $jour);
                    $date_fin = new \DateTime('' . $annee . '-' . $mois . '-' . ($jour + 1));

                    $random = rand(0, 3);

                    $title = $eventsTitle[$random] . ' ' . $annee . ' ' . $mois . ' ' . $jour;
                    $desc_public = "<p><h1>PUBLIC</h1><h2>" . $title . "</h2></p>";
                    $desc_private = "<p><h1>PRIVATE</h1><h2>" . $title . "</h2></p>";
                    $isPublic = rand(0, 2);



                    $event = new Event($admin);
                    $event->setTitle($title);

                    foreach ($users as $user) {
                        if ($user->getUsername() != "mathurin") {
                            $proposed = rand(0, 2);
                            if ($proposed !== 0) {
                                $event->addProposedTeam($user);
                                $goUser = rand(0, 3);
                                if ($goUser !== 0) {
                                    $event->addActualTeam($user);
                                }
                            }
                        }
                    }

                    $event->setPublicDescription($desc_public);
                    $event->setPrivateDescription($desc_private);
                    $event->setPublic($isPublic !== 0);
                    $event->setStartAt($date_debut);
                    $event->setEndAt($date_fin);

                    if (rand(0, 4) !== 0) {

                        $album = new EventAlbum();
                        $manager->persist($album);
                        $manager->flush();

                        $event->setAlbum($album);

                        $title = $event->getTitle();
                        $maxPicture = rand(0, 5);

                        for ($j = 0; $j < $maxPicture; $j++) {

                            $picture = new Picture($album);
                            $picture->setTitle($title . ' ' . $j);
                            $picture->setDescription('DESCRIPTION ' . $j);

                            $manager->persist($picture);
                            $manager->flush();
                            $album->addPicture($picture);
                            $manager->persist($album);
                            $manager->flush();
                        }
                    }
                    $manager->persist($event);
                    $manager->flush();
                }
            }
        }
    }

    public
            function loadEventAlbums($manager) {
        $events = $manager->getRepository('OmaracujaFrontBundle:Event')->findAll();

        foreach ($events as $event) {
            
        }
    }

}
