<?php

namespace UserBundle\Service;

use AppBundle\Includes\RoleEnums;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserService
{
    /**
     * UserService
     *
     * @var EntityManager
     * @var TokenStorage
     * @var UserManager
     */
    protected $em;
    protected $user;
    protected $userManager;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage, UserManager $userManager)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->userManager = $userManager;
    }

    /**
     * Returns users that have current access to database records
     * @return array|mixed|\Traversable
     */
    function getEntitledUsers()
    {
        if (in_array(RoleEnums::SuperAdmin, $this->user->getRoles())) {
            $allUsers = $this->userManager->findUsers();
            return $allUsers;
        } else {
            return $this->user;
        }
    }

}
