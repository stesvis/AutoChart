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
        $this->CreateCategory($manager, 'Oil Change', new \DateTime('now'), 'oil-change', 'superadmin');
        $this->CreateCategory($manager, 'Brakes', new \DateTime('now'), 'brakes', 'superadmin');
        $this->CreateCategory($manager, 'Tires', new \DateTime('now'), 'tires', 'superadmin');
        $this->CreateCategory($manager, 'Test 1', new \DateTime('now'), 'test1', 'superadmin');
        $this->CreateCategory($manager, 'Test 2', new \DateTime('now'), 'test2', 'superadmin');
        $this->CreateCategory($manager, 'Test 3', new \DateTime('now'), 'test3', 'superadmin');
    }

    private function CreateCategory(
        ObjectManager $manager,
        string $name,
        \DateTime $now,
        string $reference,
        string $username,
        string $parentCategory = null
    ) {
        $category = new Category();

        $category->setCreatedAt($now);
        $category->setCreatedBy($this->getReference($username));
        $category->setModifiedAt($now);
        $category->setModifiedBy($this->getReference($username));
        $category->setName($name);
        $category->setDescription('This is a test category');
        $category->setStatus('A');

        if (null !== $parentCategory) {
            $category->setParentCategory($this->getReference($parentCategory));
        }

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
