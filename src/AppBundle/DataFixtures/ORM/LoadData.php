<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;

class LoadData implements FixtureInterface
{
    public function load(ObjectManager $m)
    {
        $role = new Role();
        $role->setDescription('ROLE_ADMIN');
        $role->setName('ROLE_ADMIN');
        $role->setRole('ROLE_ADMIN');
        $m->persist($role);

        $role2 = new Role();
        $role2->setDescription('ROLE_USER');
        $role2->setName('ROLE_USER');
        $role2->setRole('ROLE_USER');
        $m->persist($role2);

        $m->flush();

        $user = new User();
        $user
            ->addRole($role)
            ->setUsername('admin')
            ->setPassword('123')
            ->setEmail('admin@test.com');

        $m->persist($user);
        $m->flush();
    }

}