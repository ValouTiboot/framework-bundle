<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Configuration;

/**
 * Virtual "Parameter" entity: the form reads and writes Configuration rows.
 */
final class ParameterTest extends AdminTestCase
{
    public function testFormIsPrefilledAndSaves(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/parameter/create');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('input[name="parameter[mailFrom]"]'));

        $this->submitAdminForm($crawler, 'parameter', [
            'mailFrom' => 'contact@digitix.test',
            'mailFromName' => 'Digitix',
        ]);
        self::assertResponseRedirects('/admin/parameter/create');

        $rows = $this->em()->getRepository(Configuration::class);
        $dump = var_export(array_map(static fn (Configuration $row) => [$row->getName() => $row->getValue()], $rows->findAll()), true);
        self::assertSame('contact@digitix.test', $rows->findOneBy(['name' => 'mailFrom'])?->getValue(), $dump);
        self::assertSame('Digitix', $rows->findOneBy(['name' => 'mailFromName'])?->getValue());

        $crawler = $this->client->followRedirect();
        self::assertNotEmpty($this->flashes($crawler, 'success'));
        self::assertSame('Digitix', $crawler->filter('input[name="parameter[mailFromName]"]')->attr('value'));
    }
}
