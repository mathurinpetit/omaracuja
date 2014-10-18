<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Omaracuja\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Omaracuja\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $admins = array("mathurin", "julien");
        $users = array("numa", "rebbeca", "cécile", "sahra", "mickaël", "stephane",
            "jean-yves", "clélia", "jessica", "fanny", "ludivine", "nathalie", "laure",
            "pim", "poum", "ari", "emilie", "phillipe", "alice", "marie", "joël", "flore");

        foreach ($admins as $admin) {
            $userAdmin = new User();
            $userAdmin->setUsername($admin);
            $userAdmin->setPlainPassword('12345678');
            $userAdmin->setEmail($admin . '@gmail.com');
            $userAdmin->setFirstname($admin);
            $userAdmin->setName(strtoupper($admin));
            $userAdmin->setEnabled(true);
            $userAdmin->setRoles(array('ROLE_ADMIN'));
            $manager->persist($userAdmin);
            $manager->flush();
        }
        
        foreach ($users as $userName) {
            $user = new User();
            $user->setUsername($userName);
            $user->setPlainPassword('12345678');
            $user->setEmail($userName . '@gmail.com');
            $user->setFirstname($userName);
            $user->setName(strtoupper($userName));
            $manager->persist($user);
            $manager->flush();
        }
    }

}
