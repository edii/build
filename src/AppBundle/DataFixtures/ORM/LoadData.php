<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadData implements FixtureInterface
{
    public function load(ObjectManager $m)
    {
        $user1 = new User();
        $user1
            ->setUsername('itrascastro')
            ->setPlainPassword('1234')
            ->setEmail('i.trascastro@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->setEnabled(true)
        ;
        $m->persist($user1);

        $user2 = new User();
        $user2
            ->setUsername('admin')
            ->setPlainPassword('1234')
            ->setEmail('admin@email.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(true)
        ;
        $m->persist($user2);

        $m->flush();
    }

}