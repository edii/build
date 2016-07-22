<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadData implements FixtureInterface
{
    public function load(ObjectManager $m)
    {
        $user = new User();
        $user
            ->setUsername('admin')
            ->setPassword('123')
            ->setEmail('admin@test.com');

        $m->persist($user);
        $m->flush();
    }

}