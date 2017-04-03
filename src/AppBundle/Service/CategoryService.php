<?php

namespace AppBundle\Service;

use AppBundle\Entity\Vehicle;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraints\Optional;

class CategoryService
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
     * Returns the list of categories that belong to the logged in user
     * @param $status Optional If you want you can filter by a specific status
     * @return Vehicle[]|array
     */
    public function getMyCategories($status = null): array
    {
        $filter['createdBy'] = $this->user;

        if (null !== $status) {
            $filter['status'] = $status;
        }

        $categories = $this->em->getRepository('AppBundle:Category')
            ->findBy($filter, [
                'name' => 'ASC'
            ]);

        return $categories;
    }

    /**
     * Returns the array that can be used in a ChoiceType in Forms
     * @return array
     */
    public
    function getVehiclesDropDown(): array
    {
        $vehicles = $this->getMyVehicles();

        $vehicleChoices[''] = null;

        foreach ($vehicles as $vehicle) {
            $vehicleChoices[$this->getVehicleName($vehicle)] = $vehicle->getId();
        }

        return $vehicleChoices;
    }
}
