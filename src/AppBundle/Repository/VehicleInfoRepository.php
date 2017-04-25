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
    public function findByVehicle(int $vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:VehicleInfo info')
            ->andWhere('info.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('info.name', 'ASC')
            ->getQuery();

        return $query;
    }

    /**
     * Same as above for testing purposes
     *
     * @param int $vehicleId
     * @return \Doctrine\ORM\Query
     */
    public function createFindByVehicleQuery(int $vehicleId)
    {
        $query = $this->_em->createQuery(
            '
            SELECT spec
            FORM AppBundle:VehicleInfo spec
            WHERE spec.vehicle_id = :vehicleId
            '
        );
        $query->setParameter('vehicle', $vehicleId);
        return $query;
    }
}
