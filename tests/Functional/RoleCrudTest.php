<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Role;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Form;

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

        // edit: the name and the permission matrix
        $crawler = $this->client->request('GET', '/admin/role/edit/'.$role->getId());
        self::assertResponseIsSuccessful();
        self::assertSame('Editor', $crawler->filter('input[name="role[name]"]')->attr('value'));
        self::assertGreaterThan(5, $crawler->filter('.dgtx-permissions tbody tr')->count(), 'one row per admin entity');
        self::assertCount(1, $crawler->filter('.dgtx-permissions tr.dgtx-permissions-wildcard'));

        $form = $crawler->filter('form[name="role"]')->form();
        $form['role[name]'] = 'Publisher';
        $this->tick($form, 'role[authorization][_all]', ['view', 'read']);
        $this->tick($form, 'role[authorization][cms]', ['create', 'edit']);
        $this->tick($form, 'role[authorization][user]', ['read', '*']);
        $this->client->submit($form);
        self::assertResponseRedirects('/admin/role');

        $this->em()->clear();
        $role = $this->em()->getRepository(Role::class)->find($role->getId());
        self::assertInstanceOf(Role::class, $role);
        self::assertSame('Publisher', $role->getName());
        // rows follow the configuration order (User before Cms); "all" collapses a row to the wildcard
        self::assertSame(['*' => ['view', 'read'], 'user' => ['*'], 'cms' => ['create', 'edit']], $role->getAuthorization());

        // the matrix is pre-filled from the stored permissions
        $crawler = $this->client->request('GET', '/admin/role/edit/'.$role->getId());
        self::assertNotNull($crawler->filter('input[name="role[authorization][cms][]"][value="edit"]')->attr('checked'));
        self::assertNull($crawler->filter('input[name="role[authorization][cms][]"][value="delete"]')->attr('checked'));
        self::assertStringContainsString('is-all', (string) $crawler->filter('input[name="role[authorization][user][]"]')->closest('tr')->attr('class'));

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

    /**
     * Ticks the checkboxes of a "name[]" group (addressed without the "[]")
     * whose value is in $values.
     *
     * @param string[] $values
     */
    private function tick(Form $form, string $name, array $values): void
    {
        foreach ((array) $form->get($name) as $field) {
            if ($field instanceof ChoiceFormField && \in_array($field->availableOptionValues()[0] ?? null, $values, true)) {
                $field->tick();
            }
        }
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
