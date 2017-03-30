<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{

    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBundle:Job job')
            ->andWhere('job.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('job.createdAt', 'DESC')
            ->getQuery();

        return $query;
    }
}
