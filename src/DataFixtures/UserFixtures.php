<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->userManager->createUser();
        $user->setUsername('admin');
        $user->setFirstName('Admin');
        $user->setLastName('Administrator');
        $user->setPhone('+23453675461223');
        $user->setAddress('Antananarive');
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setEmail('admin@neitic.com');
        $user->setPlainPassword('Password123');
        $user->setEnabled(true);
        $this->userManager->updateUser($user);
    }
}