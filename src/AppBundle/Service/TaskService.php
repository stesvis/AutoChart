<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TaskService
{
    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    protected $em;
    protected $user;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();

    }

    /**
     * Returns the list of tasks that belong to the logged in user
     *
     * @param $status
     * @return Vehicle[]|array
     */
    public function getMyTasks($status = null): array
    {
        $filter['createdBy'] = $this->user;

        if (null !== $status) {
            $filter['status'] = $status;
        }

        $vehicles = $this->em->getRepository('AppBundle:Task')
            ->findBy($filter, [
                'name' => 'ASC'
            ]);

        return $vehicles;
    }

}
