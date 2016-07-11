<?php
namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function myFindOneByUsernameOrEmail($username)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username OR u.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}