<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TaskFieldRepository extends EntityRepository
{

    public function findByTask($taskId)
    {
        $query = $this->createQueryBuilder('AppBundle:TaskField field')
            ->andWhere('field.task_id = :taskId')
            ->setParameter('taskId', $taskId)
            ->orderBy('field.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

}
