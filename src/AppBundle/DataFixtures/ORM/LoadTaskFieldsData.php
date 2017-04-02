<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TaskField;
use AppBundle\Includes\StatusEnums;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskFieldsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateTaskField(
            $manager,
            'Change Oil Filter',
            new \DateTime('now'),
            'field-oil-filter',
            'superadmin',
            'task-oil-change');

//        $this->CreateTaskField(
//            $manager,
//            'Oil Type',
//            new \DateTime('now'),
//            'oil-type',
//            'superadmin',
//            'task-oil-change');
//
//        $this->CreateTaskField(
//            $manager,
//            'Oil Capacity',
//            new \DateTime('now'),
//            'oil-capacity',
//            'superadmin',
//            'task-oil-change');

//        $this->CreateTaskField(
//            $manager,
//            'Tire Size',
//            new \DateTime('now'),
//            'tire-size',
//            'superadmin',
//            'tires');
    }

    /**
     * @param ObjectManager $manager
     * @param string $name
     * @param \DateTime $now
     * @param string $reference
     * @param string $userReference
     * @param string|null $taskReference
     */
    private function CreateTaskField(
        ObjectManager $manager,
        string $name,
        \DateTime $now,
        string $reference,
        string $userReference,
        string $taskReference
    ) {
        $taskField = new TaskField();

        $taskField->setName($name);
        $taskField->setTask($this->getReference($taskReference));
        $taskField->setCreatedAt($now);
        $taskField->setCreatedBy($this->getReference($userReference));
        $taskField->setModifiedAt($now);
        $taskField->setModifiedBy($this->getReference($userReference));
        $taskField->setStatus(StatusEnums::Active);

        $manager->persist($taskField);
        $manager->flush();
        $this->addReference($reference, $taskField);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 6;
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
