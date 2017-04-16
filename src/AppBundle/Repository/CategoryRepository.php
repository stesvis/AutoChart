<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Service\UserService;

class CategoryRepository extends EntityRepository
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Returns all the child categories of a given category
     *
     * @param $categoryId The parent categoryId
     * @return array
     */
    public function findByParentCategory($categoryId)
    {
        $query = $this->createQueryBuilder('AppBundle:Category cat')
            ->andWhere('cat.parentCategory = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('cat.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Takes care of user roles and category type
     *
     * @param int $id
     * @return array The category
     */
    public function findOneById(int $id)
    {
        $qb = $this->createQueryBuilder('AppBundle:Category cat');

        $query = $qb
            ->where('cat.id = :id')
            ->setParameter('id', $id)
            ->andWhere($qb->expr()->in('createdBy', $this->userService->getEntitledUsers()))
            ->orWhere('cat.type = :type')
            ->setParameter('type', 'System')
            ->getQuery();


        return $query->getResult();
    }

}
