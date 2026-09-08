<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Role;

final class RoleCrudTest extends AdminTestCase
{
    public function testCreateEditDelete(): void
    {
        $this->login();

        // create
        $crawler = $this->client->request('GET', '/admin/role/create');
        self::assertResponseIsSuccessful();

        $this->submitAdminForm($crawler, 'role', ['name' => 'Editor']);
        self::assertResponseRedirects('/admin/role');

        $role = $this->em()->getRepository(Role::class)->findOneBy(['name' => 'Editor']);
        self::assertInstanceOf(Role::class, $role);

        $crawler = $this->client->followRedirect();
        self::assertNotEmpty($this->flashes($crawler, 'success'));
        self::assertStringContainsString('Editor', $crawler->filter('table tbody')->text());

        // edit
        $crawler = $this->client->request('GET', '/admin/role/edit/'.$role->getId());
        self::assertResponseIsSuccessful();
        self::assertSame('Editor', $crawler->filter('input[name="role[name]"]')->attr('value'));

        $this->submitAdminForm($crawler, 'role', ['name' => 'Publisher']);
        self::assertResponseRedirects('/admin/role');

        $this->em()->clear();
        self::assertSame('Publisher', $this->em()->getRepository(Role::class)->find($role->getId())?->getName());

        // delete with an invalid token: refused
        $this->client->request('POST', '/admin/role/delete/'.$role->getId(), ['_token' => 'bad']);
        self::assertResponseRedirects('/admin/role');
        $crawler = $this->client->followRedirect();
        self::assertNotEmpty($this->flashes($crawler, 'danger'));
        self::assertNotNull($this->em()->getRepository(Role::class)->find($role->getId()));

        // delete with the token rendered in the list
        $form = $crawler->filter(sprintf('form[action$="/admin/role/delete/%d"]', $role->getId()))->form();
        $this->client->submit($form);
        self::assertResponseRedirects('/admin/role');

        $this->em()->clear();
        self::assertNull($this->em()->getRepository(Role::class)->find($role->getId()));
    }

    public function testValidationErrorsAreDisplayed(): void
    {
        $this->login();
        $crawler = $this->client->request('GET', '/admin/role/create');

        $crawler = $this->submitAdminForm($crawler, 'role', ['name' => '']);

        self::assertResponseIsSuccessful('an invalid form is re-displayed, not redirected');
        self::assertCount(0, $this->em()->getRepository(Role::class)->findBy(['name' => '']));
        self::assertGreaterThan(0, $crawler->filter('form[name="role"]')->count());
    }
}
