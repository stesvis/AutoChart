<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->CreateCategory($manager, 'Oil Change', new \DateTime('now'), 'oil-change', 'user1');
        $this->CreateCategory($manager, 'Brakes', new \DateTime('now'), 'brakes', 'user1');
        $this->CreateCategory($manager, 'Tires', new \DateTime('now'), 'tires', 'user1');
    }

    private function CreateCategory(
        ObjectManager $manager,
        string $name,
        \DateTime $now,
        string $reference,
        string $username
    ) {
        $category = new Category();

        $category->setCreatedBy($this->getReference($username));
        $category->setCreatedAt($now);
        $category->setModifiedAt($now);
        $category->setName($name);
        $category->setStatus('A');

        $manager->persist($category);
        $manager->flush();
        $this->addReference($reference, $category);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
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
