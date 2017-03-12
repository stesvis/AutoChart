<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Job;
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
            'user1',
            45000, 'Km',
            new \DateTime('now'),
            '2014-jeep-cherokee-task-oil-change');

        $this->CreateJob(
            $manager,
            '2008-jeep-wrangler',
            'task-oil-change',
            'user1',
            223000, 'Km',
            new \DateTime('now'),
            '2008-jeep-wrangler-task-oil-change');
    }

    private function CreateJob(
        ObjectManager $manager,
        string $vehicleReference,
        string $taskReference,
        string $userReference,
        int $mileage,
        string $mileageType,
        \DateTime $now,
        string $reference
    ) {
        $job = new Job();

        $job->setVehicle($this->getReference($vehicleReference));
        $job->setTask($this->getReference($taskReference));
        $job->setCompletedBy($this->getReference($userReference));
        $job->setMileage($mileage);
        $job->setMileageType($mileageType);
        $job->setCompletedAt($now);
        $job->setStatus('A');

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
