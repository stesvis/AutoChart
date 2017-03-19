<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
//    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
//    {
//        parent::__construct($em, $class);
//    }

    public function findByVehicle($vehicleId)
    {
        $query = $this->createQueryBuilder('AppBudle:Job job')
            ->andWhere('job.vehicle_id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('job.createdAt', 'DESC')
            ->getQuery();

        return $query;
    }
}
