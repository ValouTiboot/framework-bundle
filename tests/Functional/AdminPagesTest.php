<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Every page declared by the shipped recipe renders for an authenticated
 * super admin.
 */
final class AdminPagesTest extends AdminTestCase
{
    /** @return iterable<string, array{string}> */
    public static function pages(): iterable
    {
        yield 'dashboard' => ['/admin/dashboard/view'];
        yield 'user list' => ['/admin/user'];
        yield 'user create' => ['/admin/user/create'];
        yield 'user edit' => ['/admin/user/edit/1'];
        yield 'role list' => ['/admin/role'];
        yield 'language list' => ['/admin/language'];
        yield 'language create' => ['/admin/language/create'];
        yield 'cms list' => ['/admin/cms'];
        yield 'cms create' => ['/admin/cms/create'];
        yield 'cms category list' => ['/admin/cmsCategory'];
        yield 'meta list' => ['/admin/meta'];
        yield 'translation selection' => ['/admin/translation'];
        yield 'translation editor' => ['/admin/translation/create?translation[type]=bo&translation[locale]=fr_FR'];
        yield 'parameter' => ['/admin/parameter/create'];
        yield 'performance' => ['/admin/performance/create'];
        yield 'user list filtered' => ['/admin/user?filters[email]=valentin&filters[active]=1'];
        yield 'user list sorted' => ['/admin/user?sortBy=email&sortWay=desc'];
        yield 'cms list sorted on translated property' => ['/admin/cms?sortBy=name&sortWay=asc'];
    }

    #[DataProvider('pages')]
    public function testPageRenders(string $url): void
    {
        $this->login();
        $this->client->request('GET', $url);

        self::assertResponseIsSuccessful($url);
    }

    public function testUserListShowsTheFixtureUser(): void
    {
        $this->login();
        $crawler = $this->client->request('GET', '/admin/user');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(self::ADMIN_EMAIL, $crawler->filter('table tbody')->text());
    }

    public function testFiltersNarrowTheList(): void
    {
        $this->login();
        $crawler = $this->client->request('GET', '/admin/user?filters[email]=nobody@nowhere.tld');

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('table tbody tr'));
    }

    public function testTrailingSlashRedirects(): void
    {
        $this->login();
        $this->client->request('GET', '/admin/user/');

        self::assertResponseRedirects('/admin/user', 301);
    }
}
