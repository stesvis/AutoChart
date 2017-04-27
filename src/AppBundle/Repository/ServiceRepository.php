<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ServiceRepository extends EntityRepository
{
    /**
     * @param $vehicleId
     * @return array
     */
    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:Service service')
            ->andWhere('service.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('service.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

}
