<?php

namespace AppBundle\ViewModel;

use AppBundle\Entity\Vehicle;

class VehicleExtended extends Vehicle
{
    /**
     * @var array
     */
    private $services;

    /**
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param array $services
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }

}
