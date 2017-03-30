<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VehicleFieldDefaultRepository extends EntityRepository
{
    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:VehicleFieldDefault default')
            ->andWhere('default.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->getQuery();

        return $query->getResult();
    }
}
