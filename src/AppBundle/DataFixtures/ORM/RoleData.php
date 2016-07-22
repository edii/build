<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * @package AppBundle\DataFixtures\ORM
 */
class RoleData extends AbstractFixture
{
    public function load(ObjectManager $m)
    {
        $role = new Role();
        $role->setDescription('ROLE_ADMIN');
        $role->setName('ROLE_ADMIN');
        $role->setRole('ROLE_ADMIN');
        $m->persist($role);

        $this->addReference('ROLE_ADMIN', $role);

        $role2 = new Role();
        $role2->setDescription('ROLE_USER');
        $role2->setName('ROLE_USER');
        $role2->setRole('ROLE_USER');
        $m->persist($role2);

        $this->addReference('ROLE_USER', $role2);

        $m->flush();
    }

}