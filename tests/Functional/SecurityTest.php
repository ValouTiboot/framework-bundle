<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

final class SecurityTest extends AdminTestCase
{
    public function testAnonymousIsRedirectedToLogin(): void
    {
        foreach (['/admin/user', '/admin/dashboard/view', '/admin/user/edit/1', '/admin/cms/create'] as $url) {
            $this->client->request('GET', $url);

            self::assertResponseRedirects('/admin/', 302, sprintf('%s must redirect anonymous users', $url));
        }
    }

    public function testAnonymousCannotDelete(): void
    {
        $this->client->request('POST', '/admin/user/delete/1', ['_token' => 'whatever']);

        self::assertResponseRedirects('/admin/');
    }

    public function testDeleteRejectsGet(): void
    {
        $this->login();
        $this->client->request('GET', '/admin/role/delete/1');

        self::assertResponseStatusCodeSame(405);
    }

    public function testLoginPageIsPublic(): void
    {
        $crawler = $this->client->request('GET', '/admin/');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('input[name="_csrf_token"]'));
    }

    public function testUnknownEntityIs404(): void
    {
        $this->login();
        $this->client->request('GET', '/admin/nope');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoginFormAuthenticates(): void
    {
        $crawler = $this->client->request('GET', '/admin/');
        $form = $crawler->filter('form')->form([
            'email' => self::ADMIN_EMAIL,
            'password' => 'digitix',
        ]);

        $this->client->submit($form);
        self::assertResponseRedirects('/admin/dashboard/view');

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
    }
}
