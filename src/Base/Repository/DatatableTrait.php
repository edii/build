<?php

namespace Base\Repository;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

trait DatatableTrait
{
    /**
     * Count elements of an object
     *
     * @param array $fields
     * @param array $params
     * @return int The custom count as an integer.
     */
    public function getObjectResult(array $fields, array $params)
    {
        $data = [];
        $repoFileds = [];
        foreach ($fields as $field) {
            $repoFileds[] = 'REPO.'.$field;
        }

        $where = [];
        foreach ($params as $field => $value) {
            if ($field) {
                $where[] = 'REPO.'.$field.'=:'.$field;
            }

        }

        $result = $this->createQueryBuilder('REPO')
            ->select($repoFileds)
            ->where(implode(' AND ', $where))
            ->setParameters($params)
            ->getQuery()
            ->getResult();

        if (count($result) > 0) {
            foreach ($result as $item) {
                if (is_array($item)) {
                    $userEntity = new User();
                    foreach ($item as $field => $value) {
                        if (method_exists($userEntity, 'set'.ucfirst($field))) {
                            $userEntity->{'set'.ucfirst($field)}($value);
                        }
                    }
                    $data[] = $userEntity;
                }
            }
        }

        return $data;
    }
}
