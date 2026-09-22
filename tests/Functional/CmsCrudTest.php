<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Digitix\FrameworkBundle\Entity\Language;

/**
 * Translatable entity: one input per language, translation rows created,
 * listed, filtered and deleted along with the entity.
 */
final class CmsCrudTest extends AdminTestCase
{
    public function testTranslatableCreateEditFilterDelete(): void
    {
        $this->login();
        $language = $this->em()->getRepository(Language::class)->findOneBy(['defaultLanguage' => true]);
        self::assertNotNull($language);
        $lang = $language->getId();

        // the create form has one input per active language for translated fields
        $crawler = $this->client->request('GET', '/admin/cms/create');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter(sprintf('input[name="cms[translatableName][%d]"]', $lang)));

        $this->client->catchExceptions(false);
        $this->submitAdminForm($crawler, 'cms', [
            "translatableName][$lang" => 'Page test',
            "translatableContent][$lang" => '<p>Contenu</p>',
            "translatableMetaTitle][$lang" => 'Meta',
            "translatableRewrite][$lang" => 'page-test',
            'active' => '1',
        ]);
        self::assertResponseRedirects('/admin/cms');

        $cms = $this->em()->getRepository(Cms::class)->findOneBy([]);
        self::assertInstanceOf(Cms::class, $cms);
        self::assertCount(1, $cms->getTranslations());
        self::assertSame('Page test', $cms->getTranslations()->first()->getName());
        self::assertSame('page-test', $cms->getRewrite(), 'magic accessor reads the current language');
        self::assertNotNull($cms->getDateAdd(), 'timestamps are set by the persister');

        // list shows the translated name, filter on it works through the translation alias
        $crawler = $this->client->followRedirect();
        self::assertStringContainsString('Page test', $crawler->filter('table tbody')->text());

        $crawler = $this->client->request('GET', '/admin/cms?filters[name]=test');
        self::assertCount(1, $crawler->filter('table tbody tr'));

        $crawler = $this->client->request('GET', '/admin/cms?filters[name]=zzz');
        self::assertCount(0, $crawler->filter('table tbody .dgtx-row-actions'));
        self::assertCount(1, $crawler->filter('table tbody .dgtx-empty'));

        // edit form is pre-filled and updates the translation
        $crawler = $this->client->request('GET', '/admin/cms/edit/'.$cms->getId());
        self::assertResponseIsSuccessful();
        self::assertSame('Page test', $crawler->filter(sprintf('input[name="cms[translatableName][%d]"]', $lang))->attr('value'));

        $this->submitAdminForm($crawler, 'cms', ["translatableName][$lang" => 'Page test 2']);
        self::assertResponseRedirects('/admin/cms');

        $this->em()->clear();
        $translation = $this->em()->getRepository(CmsTranslation::class)->findOneBy(['translatable' => $cms->getId()]);
        self::assertSame('Page test 2', $translation?->getName());

        // delete cascades to translations
        $crawler = $this->client->request('GET', '/admin/cms');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('table tbody tr'), $crawler->filter('table')->html());
        $deleteForm = $crawler->filter(sprintf('form[action$="/admin/cms/delete/%d"]', $cms->getId()));
        self::assertCount(1, $deleteForm, 'id '.$cms->getId().' / '.$crawler->filter('table tbody')->html());
        $this->client->submit($deleteForm->form());
        self::assertResponseRedirects('/admin/cms');

        $this->em()->clear();
        self::assertCount(0, $this->em()->getRepository(Cms::class)->findAll());
        self::assertCount(0, $this->em()->getRepository(CmsTranslation::class)->findAll());
    }
}
