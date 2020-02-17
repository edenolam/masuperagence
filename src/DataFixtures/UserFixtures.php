<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('julien');
        $user->setPassword($this->encoder->encodePassword($user, 'julien'));
        $manager->persist($user);
        $manager->flush();

        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setPassword($this->encoder->encodePassword($userAdmin, 'admin'));
        $manager->persist($userAdmin);
        $manager->flush();
    }
}
