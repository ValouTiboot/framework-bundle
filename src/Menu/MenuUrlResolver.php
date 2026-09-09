<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Turns what a menu item points to into a URL, in a given language.
 * Returns null when the target no longer exists (route removed, CMS page
 * deleted or disabled): the item is then left out of the rendered menu.
 */
final class MenuUrlResolver implements ResetInterface
{
    /** @var array<string, array<int, string>> locale => cms id => rewrite */
    private array $cmsRewrites = [];

    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $entityManager,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function resolveItem(MenuItem $item, Language $language): ?string
    {
        return $this->resolve($item->getType(), $item->getRoute(), $item->getRouteParams(), $item->getIdEntity(), $item->getLink(), $language);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function resolve(MenuItemType $type, ?string $route, array $params, ?int $idEntity, ?string $link, Language $language): ?string
    {
        switch ($type) {
            case MenuItemType::Link:
                return null === $link || '' === trim($link) ? null : trim($link);

            case MenuItemType::Cms:
                $rewrite = null === $idEntity ? null : ($this->cmsRewrites($language)[$idEntity] ?? null);

                return null === $rewrite ? null : $this->generate(MenuItemType::CMS_ROUTE, ['entityId' => $idEntity, 'rewrite' => $rewrite]);

            case MenuItemType::Route:
                return null === $route ? null : $this->generate($route, $params);
        }
    }

    public function reset(): void
    {
        $this->cmsRewrites = [];
    }

    /**
     * @param array<string, mixed> $params
     */
    private function generate(string $route, array $params): ?string
    {
        try {
            return $this->urlGenerator->generate($route, $params);
        } catch (RoutingException $e) {
            $this->logger->warning('Menu item skipped: {message}', ['message' => $e->getMessage(), 'route' => $route]);

            return null;
        }
    }

    /**
     * Rewrites of the active CMS pages in a language, loaded once per request.
     *
     * @return array<int, string>
     */
    private function cmsRewrites(Language $language): array
    {
        $locale = (string) $language->getLocale();

        if (!isset($this->cmsRewrites[$locale])) {
            $this->cmsRewrites[$locale] = [];

            /** @var array<int, array{id: int, rewrite: string}> $rows */
            $rows = $this->entityManager->createQuery(
                'SELECT c.id AS id, t.rewrite AS rewrite FROM '.Cms::class.' c JOIN c.translations t'
                .' WHERE c.active = true AND t.language = :language'
            )->setParameter('language', $language)->getArrayResult();

            foreach ($rows as $row) {
                $this->cmsRewrites[$locale][(int) $row['id']] = (string) $row['rewrite'];
            }
        }

        return $this->cmsRewrites[$locale];
    }
}
