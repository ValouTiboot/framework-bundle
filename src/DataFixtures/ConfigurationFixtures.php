<?php

namespace Digitix\FrameworkBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Digitix\FrameworkBundle\Entity\Configuration;

class ConfigurationFixtures extends Fixture
{
    const DATAS = [
        [
            'name' => 'cache',
            'value' => 0,
        ],
        [
            'name' => 'compile',
            'value' => 0,
        ],
        [
            'name' => 'compileCss',
            'value' => 0,
        ],
        [
            'name' => 'comppileJs',
            'value' => 0,
        ],
        [
            'name' => 'debug',
            'value' => 1,
        ],
        [
            'name' => 'mailFrom',
            'value' => null,
        ],
        [
            'name' => 'maiFromName',
            'value' => null,
        ],
        [
            'name' => 'ssl',
            'value' => 1,
        ],
        [
            'name' => 'gtm',
            'value' => null,
        ],
        [
            'name' => 'tag',
            'value' => null,
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::DATAS as $config) {
            $configuration = new Configuration();
            $configuration->setName($config['name']);
            $configuration->setValue($config['value']);
            $manager->persist($configuration);
        }

        $manager->flush();
    }
}
