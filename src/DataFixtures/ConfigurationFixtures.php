<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\DataFixtures;

use Digitix\FrameworkBundle\Admin\AdminTheme;
use Digitix\FrameworkBundle\Entity\Configuration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Default rows of the global configuration (Parameter page, colour mode).
 */
class ConfigurationFixtures extends Fixture
{
    public const DATAS = [
        'mailFrom' => null,
        'mailFromName' => null,
        'ssl' => '1',
        'gtm' => null,
        'tag' => null,
        'robots' => null,
        AdminTheme::CONFIGURATION_KEY => AdminTheme::SYSTEM,
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATAS as $name => $value) {
            $manager->persist((new Configuration())->setName($name)->setValue($value));
        }

        $manager->flush();
    }
}
