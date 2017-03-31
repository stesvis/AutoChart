<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VehicleInfoRepository extends EntityRepository
{
    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:VehicleInfo info')
            ->andWhere('info.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('info.name', 'ASC')
            ->getQuery();

        return $query;
    }
}
