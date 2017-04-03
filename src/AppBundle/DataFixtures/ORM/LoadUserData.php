<?php

namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
        $this->addUser($manager, 'superadmin', 'superadmin@masterlube.com', 'dupidurs', 'Cristian', 'Merli',
            ['ROLE_SUPER_ADMIN']);
        $this->addUser($manager, 'admin', 'admin@masterlube.com', 'dupidurs', 'Jack', 'Bauer', ['ROLE_ADMIN']);
        $this->addUser($manager, 'admintest', 'admintest@masterlube.com', 'password', 'Jack', 'Bauer', ['ROLE_ADMIN']);
        $this->addUser($manager, 'user', 'user@masterlube.com', 'dupidurs', 'Walter', 'White', ['ROLE_USER']);
    }

    /**
     * @param ObjectManager $manager
     * @param               $username
     * @param               $email
     * @param               $password
     * @param               $firstName
     * @param               $lastName
     * @param array $roles
     */
    private function addUser(
        ObjectManager $manager,
        $username,
        $email,
        $password,
        $firstName,
        $lastName,
        $roles = ['ROLE_USER']
    ) {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = new User();
        $user->setUsername($username);
        $user->setUsernameCanonical($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setPlainPassword($password);
        $userManager->updatePassword($user);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles($roles);
        $user->setEnabled(true);
        //$userManager->updateUser($user, true);
        $manager->persist($user);
        $manager->flush();
        $this->addReference($username, $user);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
