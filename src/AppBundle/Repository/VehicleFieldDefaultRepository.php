<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VehicleFieldDefaultRepository extends EntityRepository
{
    /**
     * Finds all the Defaults stored for a given vehicle
     * @param $vehicleId
     * @return array
     */
    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:VehicleFieldDefault default')
            ->andWhere('default.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->getQuery();

        return $query->getResult();
    }
}
