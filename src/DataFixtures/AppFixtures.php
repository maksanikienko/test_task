<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected UserPasswordHasherInterface $hasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('admin@gmail.com');
        $user1->setRoles(['ROLE_ADMIN']);
        $user1->setPassword($this->hasher->hashPassword($user1, 'qweqwe'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('qwerty@gmail.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->hasher->hashPassword($user2, 'qwerty'));
        $manager->persist($user2);

        $manager->flush();
    }
}
