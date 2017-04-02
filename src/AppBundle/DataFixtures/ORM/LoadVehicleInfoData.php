<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\VehicleInfo;
use AppBundle\Includes\StatusEnums;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadVehicleInfoData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateVehicleInfo(
            $manager,
            'Oil Pan Plug Size',
            '16mm',
            new \DateTime('now'),
            'oil-pan-plug-size',
            'admintest',
            '2008-jeep-wrangler');

        $this->CreateVehicleInfo(
            $manager,
            'Oil Type',
            '5W-20',
            new \DateTime('now'),
            'oil-type',
            'admintest',
            '2008-jeep-wrangler');

        $this->CreateVehicleInfo(
            $manager,
            'Lugnuts Torque',
            '110 tf/lbs',
            new \DateTime('now'),
            'oil-capacity',
            'admintest',
            '2014-jeep-cherokee');

    }

    /**
     * @param ObjectManager $manager
     * @param string $name
     * @param string $value
     * @param \DateTime $now
     * @param string $reference
     * @param string $userReference
     * @param string|null $vehicleReference
     */
    private function CreateVehicleInfo(
        ObjectManager $manager,
        string $name,
        string $value,
        \DateTime $now,
        string $reference,
        string $userReference,
        string $vehicleReference
    ) {
        $vehicleInfo = new VehicleInfo();

        $vehicleInfo->setName($name);
        $vehicleInfo->setValue($value);
        $vehicleInfo->setVehicle($this->getReference($vehicleReference));

        $vehicleInfo->setCreatedAt($now);
        $vehicleInfo->setCreatedBy($this->getReference($userReference));

        $vehicleInfo->setModifiedAt($now);
        $vehicleInfo->setModifiedBy($this->getReference($userReference));

        $vehicleInfo->setStatus(StatusEnums::Active);

        $manager->persist($vehicleInfo);
        $manager->flush();
        $this->addReference($reference, $vehicleInfo);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 7;
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
