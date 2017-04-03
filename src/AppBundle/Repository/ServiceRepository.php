<?php

namespace AppBundle\Repository;


use AppBundle\Includes\StatusEnums;
use Doctrine\ORM\EntityRepository;

class ServiceRepository extends EntityRepository
{

    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:Service service')
            ->andWhere('service.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('service.createdAt', 'DESC')
            ->getQuery();

        return $query;
    }

    public function findAllActive(callable $func = null)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->where("s.status = '" . StatusEnums::Active . "''");

        if (is_callable($func)) {
            return $func($qb);
        }

        return $qb->getQuery()->getResult();
    }
}
