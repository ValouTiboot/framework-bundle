<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Admin\AdminTheme;
use Digitix\FrameworkBundle\Entity\Configuration;

/**
 * Colour mode of the admin: stored in the Configuration table, rendered on
 * <html data-theme>, changed by the switch of the user menu (JSON POST).
 */
final class ThemeTest extends AdminTestCase
{
    public function testTheStoredThemeIsRenderedAndCanBeChanged(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/dashboard/view');
        self::assertResponseIsSuccessful();
        self::assertSame('system', $crawler->filter('html')->attr('data-theme'), 'default: follow the system');
        self::assertCount(3, $crawler->filter('[data-theme-switch] [data-theme-choice]'));
        self::assertSame('system', $crawler->filter('[data-theme-switch] .active')->attr('data-theme-choice'));

        $switch = $crawler->filter('[data-theme-switch]');
        $url = $switch->attr('data-url');
        $token = $switch->attr('data-token');
        self::assertSame('/admin/tools/action/theme', $url);

        $this->client->request('POST', $url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['theme' => 'dark', '_token' => $token]));
        self::assertResponseIsSuccessful();
        self::assertSame(['theme' => 'dark'], json_decode((string) $this->client->getResponse()->getContent(), true));

        $row = $this->em()->getRepository(Configuration::class)->findOneBy(['name' => AdminTheme::CONFIGURATION_KEY]);
        self::assertNotNull($row);
        self::assertSame('dark', $row->getValue());

        $crawler = $this->client->request('GET', '/admin/dashboard/view');
        self::assertSame('dark', $crawler->filter('html')->attr('data-theme'));
        self::assertSame('dark', $crawler->filter('[data-theme-switch] .active')->attr('data-theme-choice'));
    }

    public function testBadRequestsAreRefused(): void
    {
        $this->login();
        $token = $this->client->request('GET', '/admin/dashboard/view')->filter('[data-theme-switch]')->attr('data-token');

        $this->client->request('GET', '/admin/tools/action/theme');
        self::assertResponseStatusCodeSame(405);

        $this->client->request('POST', '/admin/tools/action/theme', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['theme' => 'dark', '_token' => 'wrong']));
        self::assertResponseStatusCodeSame(403);

        $this->client->request('POST', '/admin/tools/action/theme', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['theme' => 'blue', '_token' => $token]));
        self::assertResponseStatusCodeSame(422);

        $crawler = $this->client->request('GET', '/admin/dashboard/view');
        self::assertSame('system', $crawler->filter('html')->attr('data-theme'), 'nothing was stored');
    }

    public function testAnUnknownStoredValueFallsBackToSystem(): void
    {
        $row = $this->em()->getRepository(Configuration::class)->findOneBy(['name' => AdminTheme::CONFIGURATION_KEY]);
        self::assertNotNull($row);
        $row->setValue('sepia');
        $this->em()->flush();
        $this->em()->clear();

        $this->login();
        $crawler = $this->client->request('GET', '/admin/dashboard/view');
        self::assertSame('system', $crawler->filter('html')->attr('data-theme'));
    }
}
