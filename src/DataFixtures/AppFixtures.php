<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('fr@ooo.fr');
        
        /* azerty */ $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$Pw1Rda+gd/3emyiO9M9Upg$S9OqeFtphdhH5T4Jt0ZvXvf2wbVHrxm3DtI6X1TCXWU');

        $manager->persist($user);
        $manager->flush();
    }
}