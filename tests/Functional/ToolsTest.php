<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Repository\TranslationRepository;

/**
 * Tools page: maintenance actions behind a POST and a token.
 */
final class ToolsTest extends AdminTestCase
{
    public function testActionsNeedAPostWithAValidToken(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/tools/view');
        self::assertResponseIsSuccessful();
        self::assertCount(3, $crawler->filter('.dgtx-tool form'));
        self::assertStringContainsString(\PHP_VERSION, $crawler->filter('.dgtx-facts')->text());

        $this->client->request('GET', '/admin/tools/action/translations_extract');
        self::assertResponseRedirects('/admin/tools/view');
        self::assertNotEmpty($this->flashes($this->client->followRedirect(), 'danger'), 'GET is refused');

        $this->client->request('POST', '/admin/tools/action/cache_clear', ['_token' => 'wrong']);
        self::assertResponseRedirects('/admin/tools/view');
        self::assertNotEmpty($this->flashes($this->client->followRedirect(), 'danger'), 'a bad token is refused');
    }

    public function testTranslationActionsRun(): void
    {
        $this->login();
        $crawler = $this->client->request('GET', '/admin/tools/view');
        $token = $crawler->filter('.dgtx-tool input[name="_token"]')->first()->attr('value');

        $this->client->request('POST', '/admin/tools/action/translations_extract', ['_token' => $token], [], ['HTTP_REFERER' => 'http://localhost/admin/dashboard/view']);
        self::assertResponseRedirects('http://localhost/admin/dashboard/view', null, 'back to the page the button was on');
        self::assertStringContainsString('Clés de traduction actualisées', implode(' ', $this->flashes($this->client->followRedirect(), 'success')));
        self::assertGreaterThan(50, static::getContainer()->get(TranslationRepository::class)->count([]));

        // the browser sends the previous page as referer: from the tools page, back to the tools page
        $this->client->request('POST', '/admin/tools/action/translations_compile', ['_token' => $token], [], ['HTTP_REFERER' => 'http://localhost/admin/tools/view']);
        self::assertResponseRedirects('http://localhost/admin/tools/view');
        self::assertStringContainsString('Catalogues de traduction compilés', implode(' ', $this->flashes($this->client->followRedirect(), 'success')));
    }
}
