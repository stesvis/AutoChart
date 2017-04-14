<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
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

}
