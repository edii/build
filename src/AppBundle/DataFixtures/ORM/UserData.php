<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @package AppBundle\DataFixtures\ORM
 */
class UserData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $roleAdmin = $this->getReference('ROLE_ADMIN');
        $user
            ->addRole($manager->getRepository('AppBundle:Role')->findOneById($roleAdmin->getId()))
            ->setUsername('admin')
            ->setEmail('admin@gmail.com')
            ->setPlainPassword('123');

        // $pass = $this->container->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
        $user->setPassword('123');

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\RoleData',
        ];
    }
}