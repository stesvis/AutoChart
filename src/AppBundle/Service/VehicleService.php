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
     *
     * @param $status
     * @return Vehicle[]|array
     */
    public function getMyVehicles($status = null): array
    {
        $filter['createdBy'] = $this->user;

        if (null !== $status) {
            $filter['status'] = $status;
        }

        $vehicles = $this->em->getRepository('AppBundle:Vehicle')
            ->findBy($filter, [
                'year' => 'DESC'
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

}
