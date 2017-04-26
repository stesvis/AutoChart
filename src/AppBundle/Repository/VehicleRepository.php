<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VehicleRepository extends EntityRepository
{
    public function findByUser(int $userId)
    {
        $query = $this->createQueryBuilder('AppBundle:Vehicle v')
            ->Where('v.created_by_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('v.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
