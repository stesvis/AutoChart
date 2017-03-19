<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Job;
use AppBundle\Includes\StatusEnums;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadJobData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateJob(
            $manager,
            '2014-jeep-cherokee',
            'task-oil-change',
            'superadmin',
            45000,
            new \DateTime('now'),
            '2014-jeep-cherokee-task-oil-change');

        $this->CreateJob(
            $manager,
            '2008-jeep-wrangler',
            'task-oil-change',
            'superadmin',
            223000,
            new \DateTime('now'),
            '2008-jeep-wrangler-task-oil-change');
    }

    private function CreateJob(
        ObjectManager $manager,
        string $vehicleReference,
        string $taskReference,
        string $userReference,
        int $mileage,
        \DateTime $now,
        string $reference
    ) {
        $job = new Job();

        $job->setVehicle($this->getReference($vehicleReference));
        $job->setTask($this->getReference($taskReference));
        $job->setCreatedAt($now);
        $job->setCreatedBy($this->getReference($userReference));
        $job->setModifiedAt($now);
        $job->setModifiedBy($this->getReference($userReference));
        $job->setMileage($mileage);
        $job->setStatus(StatusEnums::Active);

        $manager->persist($job);
        $manager->flush();
        $this->addReference($reference, $job);
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
