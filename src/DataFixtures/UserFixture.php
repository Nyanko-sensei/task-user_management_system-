<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setUsername('Main user');
        $manager->persist($user);

        $manager->flush();
    }
}
