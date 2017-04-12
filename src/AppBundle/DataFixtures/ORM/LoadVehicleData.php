<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Vehicle;
use AppBundle\Includes\StatusEnums;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadVehicleData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateVehicle(
            $manager,
            2008, 'Jeep', 'Wrangler',
            'Rubicon', 'Manual', 'Gas', '3.8L V6', 5, 'Yellow',
            12500, new \DateTime('2015-09-15'),
            new \DateTime('now'),
            '2008-jeep-wrangler', 'admintest');
        $this->CreateVehicle(
            $manager,
            2014, 'Jeep', 'Cherokee',
            'Trailhawk', 'Auto', 'Gas', '3.2L V6', 5, 'Green',
            45000, new \DateTime('2014-05-03'),
            new \DateTime('now'),
            '2014-jeep-cherokee', 'admintest');
        $this->CreateVehicle(
            $manager,
            2007, 'Ford', 'Focus',
            'SE', 'Auto', 'Gas', '2.0L', 5, 'Gold',
            8000, new \DateTime('2008-08-05'),
            new \DateTime('now'),
            '2007-ford-focus', 'admintest');
        $this->CreateVehicle(
            $manager,
            2006, 'Honda', 'Odyssey',
            'EXL', 'Auto', 'Gas', '3.5L V6', 8, 'Silver',
            11000, new \DateTime('2016-09-27'),
            new \DateTime('now'),
            '2006-honda-odyssey', 'admintest');
    }

    private function CreateVehicle(
        ObjectManager $manager,
        int $year,
        string $make,
        string $model,
        string $trim,
        string $tranny,
        string $fuel,
        string $engine,
        string $passengers,
        string $color,
        float $price,
        \DateTime $purchasedAt,
        \DateTime $now,
        string $reference,
        string $userReference
    ) {
        $vehicle = new Vehicle();

        $vehicle->setName($year . ' ' . $make . ' ' . $model);
//        $vehicle->setName('test');
        $vehicle->setYear($year);
        $vehicle->setMake($make);
        $vehicle->setModel($model);
        $vehicle->setTrim($trim);
        $vehicle->setDescription('This is a test vehicle.');
        $vehicle->setTransmissionType($tranny);
        $vehicle->setFuelType($fuel);
        $vehicle->setEngineSize($engine);
        $vehicle->setPassengers($passengers);
        $vehicle->setColor($color);
        $vehicle->setPurchasedAt($purchasedAt);
        $vehicle->setCreatedAt($now);
        $vehicle->setCreatedBy($this->getReference($userReference));
        $vehicle->setPrice($price);
        $vehicle->setModifiedAt(new \DateTime('now'));
        $vehicle->setModifiedBy($this->getReference($userReference));
        $vehicle->setStatus(StatusEnums::Active);

        $manager->persist($vehicle);
        $manager->flush();

        $this->addReference($reference, $vehicle);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
