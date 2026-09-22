<?php

namespace Digitix\FrameworkBundle\DataFixtures;

use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Digitix\FrameworkBundle\Entity\Language;

class LanguageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dateTime = new DateTime();
        $language = new Language();
        $language->setName('Francais');
        $language->setIso('fr');
        $language->setLocale('fr_FR');
        $language->setFormatDate('d/m/Y');
        $language->setFormatDatetime('d/m/Y H:i:s');
        $language->setActive(true);
        $language->setDefaultLanguage(true);
        $language->setRtl(false);
        $language->setDateAdd($dateTime);
        $language->setDateUpd($dateTime);

        $manager->persist($language);
        $manager->flush();
    }
}
