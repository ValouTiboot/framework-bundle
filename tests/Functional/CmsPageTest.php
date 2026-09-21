<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Digitix\FrameworkBundle\Entity\Language;

/**
 * Public CMS page served by the bundle (front_cms_show) with its default template.
 */
final class CmsPageTest extends AdminTestCase
{
    public function testThePageRendersWithTheBundleTemplateAndABreadcrumb(): void
    {
        $this->createCms('About us', 'about-us');

        $crawler = $this->client->request('GET', '/about-us.html');

        self::assertResponseIsSuccessful();
        self::assertSame('About us', $crawler->filter('h1')->text());
        self::assertStringContainsString('<p>Hello</p>', (string) $this->client->getResponse()->getContent());
        // home (test_home is "/") then the page
        self::assertCount(2, $crawler->filter('.breadcrumb-item'));
        self::assertSame('/', $crawler->filter('.breadcrumb-item a')->attr('href'));
    }

    public function testThePageMetaComesFromTheRecord(): void
    {
        $this->createCms('About us', 'about-us');

        $crawler = $this->client->request('GET', '/about-us.html');

        self::assertResponseIsSuccessful();
        self::assertSame('About us', $crawler->filter('title')->text());
    }

    public function testTheFormerIdPrefixedUrlRedirectsPermanently(): void
    {
        $cms = $this->createCms('About us', 'about-us');

        $this->client->request('GET', sprintf('/%d-old-slug.html', $cms->getId()));

        self::assertResponseRedirects('/about-us.html', 301);
    }

    public function testAnInactivePageIs404(): void
    {
        $cms = $this->createCms('Draft', 'draft')->setActive(false);
        $this->em()->flush();

        $this->client->request('GET', '/draft.html');

        self::assertResponseStatusCodeSame(404);
    }

    public function testAnUnknownPageIs404(): void
    {
        $this->client->request('GET', '/nope.html');
        self::assertResponseStatusCodeSame(404);

        $this->client->request('GET', '/999-nope.html');
        self::assertResponseStatusCodeSame(404);
    }

    public function testTheProjectRoutesStillWinOverTheCmsCatchAll(): void
    {
        $this->createCms('Contact', 'contact');

        $this->client->request('GET', '/contact.html');

        // the bundle's own contact route, not the CMS one (whatever the contact page renders in this test app)
        self::assertSame('front_contact', $this->client->getRequest()->attributes->get('_route'));
    }

    private function createCms(string $name, string $rewrite): Cms
    {
        $language = $this->em()->getRepository(Language::class)->findOneBy(['defaultLanguage' => true]);
        self::assertInstanceOf(Language::class, $language);

        $cms = (new Cms())->setActive(true)->setDateAdd(new \DateTime())->setDateUpd(new \DateTime());
        $cms->addTranslation((new CmsTranslation())
            ->setLanguage($language)
            ->setName($name)
            ->setContent('<p>Hello</p>')
            ->setMetaTitle($name)
            ->setRewrite($rewrite));

        $this->em()->persist($cms);
        $this->em()->flush();

        return $cms;
    }
}
