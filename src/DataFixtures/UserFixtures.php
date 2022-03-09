<?php

namespace Digitix\FrameworkBundle\DataFixtures;

use DateTime;
use Doctrine\Persistence\ObjectManager;
use Digitix\FrameworkBundle\Entity\Role;
use Digitix\FrameworkBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $dateTime = new DateTime();

        $role = new Role();
        $role->setName('SuperAdmin');
        $role->setAuthorization([]);

        $user = new User();
        $user
            ->setEmail('valentin@tiboott.com')
            ->setRole($role)
            ->setLastname('Thibault')
            ->setFirstname('Valentin')
            ->setActive(true)
            ->setDateAdd($dateTime)
            ->setDateUpd($dateTime)
            ->setPassword($this->passwordHasher->hashPassword($user,'digitix'));
        ;

        $manager->persist($role);
        $manager->persist($user);
        $manager->flush();
    }
}
