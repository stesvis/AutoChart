<?php

namespace AppBundle\Service;

use AppBundle\Entity\Vehicle;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class VehicleService
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
     * Returns the list of vehicles that belong to the logged in user
     * @return Vehicle[]|array
     */
    public function getMyVehicles(): array
    {
        $vehicles = $this->em->getRepository('AppBundle:Vehicle')
            ->findBy([
                //'status' => StatusEnums::Active,
                'createdBy' => $this->user
            ]);

        return $vehicles;
    }

    /**
     * Returns the full name of a vehicle as Year Make Model
     * @param Vehicle $vehicle
     * @return string
     */
    public function getVehicleName(Vehicle $vehicle): string
    {
        return "{$vehicle->getYear()} {$vehicle->getMake()} {$vehicle->getModel()}";
    }

    /**
     * Returns the array that can be used in a ChoiceType in Forms
     * @return array
     */
    public function getVehiclesDropDown(): array
    {
        $vehicles = $this->getMyVehicles();

        $vehicleChoices[''] = null;

        foreach ($vehicles as $vehicle) {
            $vehicleChoices[$this->getVehicleName($vehicle)] = $vehicle->getId();
        }

        return $vehicleChoices;
    }
}
