<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setPassword($this->hasher->hashPassword($admin,'admin'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setFirstName('BouDouma');
        $admin->setLastName('Chaker');
        $admin->setPhone('+216 99 999 999');
        $admin->setAge(99);
        $admin->setRegion('Tunis');
        $admin->setCreatedAt(new \DateTime('2000-01-01 00:00:00'));


        $user = new User();
        $user->setEmail("user@gmail.com");
        $user->setPassword($this->hasher->hashPassword($user,'user'));
        $user->setRoles(['ROLE_USER']);
        $user->setFirstName('Chaker');

        $manager->persist($admin);
        $manager->persist($user);
        $manager->flush();
    }
}
