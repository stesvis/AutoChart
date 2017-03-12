<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateTask($manager, 'Oil Change', new \DateTime('now'), 'task-oil-change', 'user1', 'oil-change');
        $this->CreateTask($manager, 'Oil Filter Change', new \DateTime('now'), 'task-oil-filter-change', 'user1',
            'oil-change');
        $this->CreateTask($manager, 'Check Oil Level', new \DateTime('now'), 'task-check-oil-level', 'user1',
            'oil-change');
        $this->CreateTask($manager, 'Dirty Oil Disposal', new \DateTime('now'), 'task-dirty-oil-disposal', 'user1',
            'oil-change');
        $this->CreateTask($manager, 'Brake Pads Replacement', new \DateTime('now'), 'task-brake-pads-replacement',
            'user2', 'brakes');
        $this->CreateTask($manager, 'Front Rotors Replacement', new \DateTime('now'), 'task-front-rotors-replacement',
            'user2', 'brakes');
    }

    private function CreateTask(
        ObjectManager $manager,
        string $name,
        \DateTime $now,
        string $reference,
        string $userReference,
        string $categoryReference = null
    ) {
        $task = new Task();

        $task->setCategory($this->getReference($categoryReference));
        $task->setCreatedAt($now);
        $task->setCreatedBy($this->getReference($userReference));
        $task->setModifiedAt($now);
        $task->setModifiedBy($this->getReference($userReference));
        $task->setTitle($name);
        $task->setDescription('This is a test task');
        $task->setStatus('A');
        $task->setType('System');

        $manager->persist($task);
        $manager->flush();
        $this->addReference($reference, $task);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 4;
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
