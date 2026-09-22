<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\DataFixtures\ConfigurationFixtures;
use Digitix\FrameworkBundle\DataFixtures\LanguageFixtures;
use Digitix\FrameworkBundle\DataFixtures\UserFixtures;
use Digitix\FrameworkBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Boots the test application with a fresh SQLite schema and the bundle
 * fixtures (one SuperAdmin, one default language, the configuration rows).
 */
abstract class AdminTestCase extends WebTestCase
{
    public const ADMIN_EMAIL = 'valentin@tiboott.com';
    public const FIREWALL = 'dgtx_admin';

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        // keep the same kernel (and thus the in-memory SQLite connection) across requests
        $this->client->disableReboot();
        $this->resetDatabase();
    }

    protected function em(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function login(): User
    {
        $user = $this->em()->getRepository(User::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        self::assertInstanceOf(User::class, $user, 'fixture user missing');

        $this->client->loginUser($user, self::FIREWALL);

        return $user;
    }

    /**
     * Submits the named admin form of the current page with the given values.
     *
     * @param array<string, mixed> $values field name => value, without the form name prefix
     */
    protected function submitAdminForm(Crawler $crawler, string $formName, array $values): Crawler
    {
        $form = $crawler->filter(sprintf('form[name="%s"]', $formName))->form();

        foreach ($values as $field => $value) {
            $form[sprintf('%s[%s]', $formName, $field)] = $value;
        }

        return $this->client->submit($form);
    }

    /** @return string[] flash messages of the given level rendered on the page */
    protected function flashes(Crawler $crawler, string $level): array
    {
        return $crawler->filter(sprintf('.alert-%s', $level))->each(static fn (Crawler $node) => trim($node->text()));
    }

    private function resetDatabase(): void
    {
        $em = $this->em();
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        (new LanguageFixtures())->load($em);
        (new ConfigurationFixtures())->load($em);
        (new UserFixtures(static::getContainer()->get(UserPasswordHasherInterface::class)))->load($em);

        $em->clear();
    }
}
