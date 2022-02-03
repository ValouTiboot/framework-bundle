<?php

namespace Digitix\FrameworkBundle\DataFixtures;

use DateTime;
use Doctrine\Persistence\ObjectManager;
use Digitix\FrameworkBundle\Entity\Role;
use Digitix\FrameworkBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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
            ->setPassword($this->passwordEncoder->encodePassword($user,'digitix'));
        ;

        $manager->persist($role);
        $manager->persist($user);
        $manager->flush();
    }
}
