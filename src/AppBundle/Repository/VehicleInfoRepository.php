<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VehicleInfoRepository extends EntityRepository
{
    /**
     * Returns the query to find vehicled details by vehicle
     *
     * @param $vehicleId
     * @return \Doctrine\ORM\Query
     */
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
