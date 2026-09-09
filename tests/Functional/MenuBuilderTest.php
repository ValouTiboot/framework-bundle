<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Digitix\FrameworkBundle\Entity\MenuTranslation;
use Digitix\FrameworkBundle\Menu\MenuItemType;

/**
 * Menu builder page, JSON save (diff, validation) and front rendering
 * through dgtx_menu().
 */
final class MenuBuilderTest extends AdminTestCase
{
    private const JSON = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'];

    public function testBuilderPageRenders(): void
    {
        $menu = $this->createMenu('main');
        $this->createCms('About us', 'about-us');
        $this->login();

        $crawler = $this->client->request('GET', '/admin/menu/view/'.$menu->getId());

        self::assertResponseIsSuccessful();
        $builder = $crawler->filter('#dgtx-menu-builder');
        self::assertCount(1, $builder);
        $config = json_decode((string) $builder->attr('data-config'), true);
        self::assertIsArray($config);
        self::assertSame('/admin/menu/action/save/'.$menu->getId(), $config['saveUrl']);
        self::assertSame(3, $config['maxDepth']);
        self::assertSame('fr_FR', $builder->attr('data-locale'));
        self::assertStringContainsString('About us', $crawler->filter('#dgtx-menu-tab-cms')->text());
        self::assertCount(1, $crawler->filter('[data-locale-switch] [data-locale="fr_FR"]'));
        self::assertCount(0, $crawler->filter('li.dgtx-menu-item'));
    }

    public function testSaveRequiresAValidToken(): void
    {
        $menu = $this->createMenu('main');
        $this->login();

        $this->post($menu, [], 'wrong');

        self::assertResponseStatusCodeSame(403);
        self::assertSame(0, $this->em()->getRepository(MenuItem::class)->count([]));
    }

    public function testSaveCreatesUpdatesAndDeletesItems(): void
    {
        $menu = $this->createMenu('main');
        $cms = $this->createCms('About us', 'about-us');
        $this->login();
        $token = $this->token($menu);

        $this->post($menu, [
            $this->link('n1', 'https://example.com', 'Exemple', 0, target: '_blank'),
            $this->cms('n2', (int) $cms->getId(), 1),
            $this->route('n3', 'test_home', 'Home', 0, parent: 'n2'),
            $this->link('n4', 'https://digitix.agency', 'Digitix', 0, parent: 'n3'),
        ], $token);

        self::assertResponseIsSuccessful();
        $ids = $this->json()['ids'];
        self::assertCount(4, $ids);

        $this->em()->clear();
        $items = $this->items($menu);
        self::assertCount(4, $items);

        $n1 = $items[$ids['n1']];
        self::assertSame(MenuItemType::Link, $n1->getType());
        self::assertSame('https://example.com', $n1->getLink());
        self::assertSame('_blank', $n1->getTarget());
        self::assertSame('Exemple', $n1->findTranslation($this->defaultLanguage()->getId())?->getName());
        self::assertSame(0, $n1->getDepth());

        $n2 = $items[$ids['n2']];
        self::assertSame(MenuItemType::Cms, $n2->getType());
        self::assertSame($cms->getId(), $n2->getIdEntity());
        self::assertSame(1, $n2->getPosition());

        $n3 = $items[$ids['n3']];
        self::assertSame($ids['n2'], $n3->getParent()?->getId());
        self::assertSame(1, $n3->getDepth());
        self::assertSame(MenuItemType::Route, $n3->getType());

        self::assertSame(2, $items[$ids['n4']]->getDepth());

        // second save: swap the two roots, drop the deepest item, keep the ids
        $this->post($menu, [
            $this->cms('i'.$ids['n2'], (int) $cms->getId(), 0, id: $ids['n2']),
            $this->link('i'.$ids['n1'], 'https://example.com', 'Exemple modifié', 1, id: $ids['n1'], active: false),
            $this->route('i'.$ids['n3'], 'test_home', 'Home', 0, parent: 'i'.$ids['n2'], id: $ids['n3']),
        ], $token);

        self::assertResponseIsSuccessful();
        $this->em()->clear();
        $items = $this->items($menu);

        self::assertCount(3, $items, 'the removed item is deleted');
        self::assertArrayHasKey($ids['n1'], $items, 'ids survive a save');
        self::assertSame(1, $items[$ids['n1']]->getPosition());
        self::assertFalse($items[$ids['n1']]->isActive());
        self::assertSame('Exemple modifié', $items[$ids['n1']]->findTranslation($this->defaultLanguage()->getId())?->getName());
        self::assertSame(0, $items[$ids['n2']]->getPosition());
    }

    public function testSaveRejectsInvalidTrees(): void
    {
        $menu = $this->createMenu('main');
        $this->login();
        $token = $this->token($menu);

        // 4 levels when 3 are allowed
        $this->post($menu, [
            $this->link('a', 'https://a.test', 'A', 0),
            $this->link('b', 'https://b.test', 'B', 0, parent: 'a'),
            $this->link('c', 'https://c.test', 'C', 0, parent: 'b'),
            $this->link('d', 'https://d.test', 'D', 0, parent: 'c'),
        ], $token);
        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('3 levels', $this->json()['error']);

        $this->post($menu, [$this->link('a', 'https://a.test', 'A', 0, parent: 'ghost')], $token);
        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('unknown parent', $this->json()['error']);

        $this->post($menu, [$this->link('a', 'https://a.test', 'A', 0, id: 999999)], $token);
        self::assertResponseStatusCodeSame(422);

        $this->post($menu, [['key' => 'a', 'type' => 'link', 'label' => 'A', 'link' => '']], $token);
        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('needs a URL', $this->json()['error']);

        self::assertSame(0, $this->em()->getRepository(MenuItem::class)->count([]), 'nothing is written when the tree is invalid');
    }

    public function testFrontRenderingFollowsTheSavedTree(): void
    {
        $menu = $this->createMenu('main');
        $this->createMenu('footer', false);
        $cms = $this->createCms('About us', 'about-us');
        $this->login();
        $token = $this->token($menu);

        $this->post($menu, [
            $this->link('n1', 'https://example.com', 'Exemple', 0, target: '_blank', cssClass: 'highlight'),
            $this->cms('n2', (int) $cms->getId(), 1),
            $this->route('n3', 'test_home', 'Accueil', 0, parent: 'n2'),
            $this->link('n4', 'https://hidden.test', 'Caché', 2, active: false),
            $this->link('n5', 'https://orphan.test', 'Orphelin', 0, parent: 'n4'),
        ], $token);
        self::assertResponseIsSuccessful();
        $ids = $this->json()['ids'];

        $crawler = $this->client->request('GET', '/');
        self::assertResponseIsSuccessful();

        $roots = $crawler->filter('header > ul.menu > li.menu-item');
        self::assertCount(2, $roots, 'the inactive item and its child are left out');

        $first = $roots->eq(0);
        self::assertSame('Exemple', $first->filter('a')->text());
        self::assertSame('https://example.com', $first->filter('a')->attr('href'));
        self::assertSame('_blank', $first->filter('a')->attr('target'));
        self::assertStringContainsString('highlight', (string) $first->attr('class'));

        $second = $roots->eq(1);
        self::assertSame('About us', $second->filter('a')->first()->text(), 'a CMS item without title takes the page name');
        self::assertSame(sprintf('/%d-about-us.html', $cms->getId()), $second->filter('a')->first()->attr('href'));
        self::assertStringContainsString('has-children', (string) $second->attr('class'));
        self::assertStringContainsString('active', (string) $second->attr('class'), 'ancestor of the current page');

        $child = $second->filter('ul.sub-menu > li.menu-item');
        self::assertCount(1, $child);
        self::assertSame('/', $child->filter('a')->attr('href'));
        self::assertSame('page', $child->filter('a')->attr('aria-current'));
        self::assertStringContainsString('current', (string) $child->attr('class'));

        self::assertCount(0, $crawler->filter('footer ul'), 'a disabled menu and an unknown code render nothing');

        // the cached rendering is dropped on save
        $this->post($menu, [$this->link('i'.$ids['n1'], 'https://example.com', 'Renommé', 0, id: $ids['n1'])], $token);
        self::assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/');
        self::assertCount(1, $crawler->filter('header > ul.menu > li.menu-item'));
        self::assertSame('Renommé', $crawler->filter('header a')->text());
    }

    // --- helpers -------------------------------------------------------------------

    /** @param array<int, array<string, mixed>> $items */
    private function post(Menu $menu, array $items, string $token): void
    {
        $this->client->request(
            'POST',
            '/admin/menu/action/save/'.$menu->getId(),
            [],
            [],
            self::JSON,
            (string) json_encode(['items' => $items, '_token' => $token])
        );
    }

    /** @return array<string, mixed> */
    private function json(): array
    {
        $data = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);

        return $data;
    }

    private function token(Menu $menu): string
    {
        $crawler = $this->client->request('GET', '/admin/menu/view/'.$menu->getId());
        $config = json_decode((string) $crawler->filter('#dgtx-menu-builder')->attr('data-config'), true);
        self::assertIsArray($config);

        return (string) $config['token'];
    }

    /** @return array<string, mixed> */
    private function link(string $key, string $url, string $title, int $position, ?string $parent = null, ?int $id = null, ?string $target = null, bool $active = true, ?string $cssClass = null): array
    {
        return ['key' => $key, 'id' => $id, 'parent' => $parent, 'position' => $position, 'type' => 'link', 'link' => $url, 'label' => $title, 'titles' => ['fr_FR' => $title], 'target' => $target, 'active' => $active, 'cssClass' => $cssClass];
    }

    /** @return array<string, mixed> */
    private function cms(string $key, int $cmsId, int $position, ?string $parent = null, ?int $id = null): array
    {
        return ['key' => $key, 'id' => $id, 'parent' => $parent, 'position' => $position, 'type' => 'cms', 'idEntity' => $cmsId, 'label' => 'About us', 'titles' => ['fr_FR' => '']];
    }

    /** @return array<string, mixed> */
    private function route(string $key, string $route, string $title, int $position, ?string $parent = null, ?int $id = null): array
    {
        return ['key' => $key, 'id' => $id, 'parent' => $parent, 'position' => $position, 'type' => 'route', 'route' => $route, 'params' => [], 'label' => $title, 'titles' => ['fr_FR' => $title]];
    }

    /** @return array<int, MenuItem> indexed by id */
    private function items(Menu $menu): array
    {
        $items = [];
        foreach ($this->em()->getRepository(MenuItem::class)->findBy(['menu' => $menu->getId()]) as $item) {
            $items[(int) $item->getId()] = $item;
        }

        return $items;
    }

    private function defaultLanguage(): Language
    {
        $language = $this->em()->getRepository(Language::class)->findOneBy(['defaultLanguage' => true]);
        self::assertInstanceOf(Language::class, $language);

        return $language;
    }

    private function createMenu(string $code, bool $active = true): Menu
    {
        $menu = (new Menu())->setCode($code)->setActive($active)->setDateAdd(new \DateTime())->setDateUpd(new \DateTime());
        $menu->addTranslation((new MenuTranslation())->setLanguage($this->defaultLanguage())->setName(ucfirst($code).' menu'));

        $this->em()->persist($menu);
        $this->em()->flush();

        return $menu;
    }

    private function createCms(string $name, string $rewrite): Cms
    {
        $cms = (new Cms())->setActive(true)->setDateAdd(new \DateTime())->setDateUpd(new \DateTime());
        $cms->addTranslation((new CmsTranslation())
            ->setLanguage($this->defaultLanguage())
            ->setName($name)
            ->setMetaTitle($name)
            ->setRewrite($rewrite));

        $this->em()->persist($cms);
        $this->em()->flush();

        return $cms;
    }
}
