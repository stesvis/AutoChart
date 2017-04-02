<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Service;
use AppBundle\Includes\StatusEnums;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServiceData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateService(
            $manager,
            '2014-jeep-cherokee',
            'task-oil-change',
            'superadmin',
            45000,
            new \DateTime('now'),
            '2014-jeep-cherokee-task-oil-change');

        $this->CreateService(
            $manager,
            '2008-jeep-wrangler',
            'task-oil-change',
            'superadmin',
            223000,
            new \DateTime('now'),
            '2008-jeep-wrangler-task-oil-change');
    }

    private function CreateService(
        ObjectManager $manager,
        string $vehicleReference,
        string $taskReference,
        string $userReference,
        int $mileage,
        \DateTime $now,
        string $reference
    ) {
        $service = new Service();

        $service->setVehicle($this->getReference($vehicleReference));
        $service->setTask($this->getReference($taskReference));
        $service->setNotes('This is a test');
        $service->setCreatedAt($now);
        $service->setCreatedBy($this->getReference($userReference));
        $service->setModifiedAt($now);
        $service->setModifiedBy($this->getReference($userReference));
        $service->setMileage($mileage);
        $service->setStatus(StatusEnums::Active);

        $manager->persist($service);
        $manager->flush();
        $this->addReference($reference, $service);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
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
