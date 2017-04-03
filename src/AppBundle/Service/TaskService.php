<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Service\UserService;

class TaskService
{
    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    protected $em;
    protected $user;
    protected $userService;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage, UserService $userService)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->userService = $userService;
    }

    /**
     * Returns the list of tasks that belong to the logged in user
     *
     * @param $status
     * @return Vehicle[]|array
     */
    public function getMyTasks($status = null): array
    {
        $filter['createdBy'] = $this->userService->getEntitledUsers();

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
