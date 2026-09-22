<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Twig;

use Digitix\FrameworkBundle\Menu\MenuProvider;
use Digitix\FrameworkBundle\Menu\MenuTree;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Front rendering of the menus built in the admin:
 *
 *   {{ dgtx_menu('main') }}
 *   {{ dgtx_menu('footer', {class: 'footer-menu', depth: 1, locale: app.request.locale}) }}
 *   {% set tree = dgtx_menu_tree('main') %}   custom markup
 *
 * Options: template (default @DigitixFramework/front/menu/menu.html.twig,
 * override it in templates/bundles/DigitixFrameworkBundle/front/menu/),
 * class (root <ul> class, default "menu"), depth (levels rendered), locale.
 */
final class MenuExtension extends AbstractExtension
{
    public const TEMPLATE = '@DigitixFramework/front/menu/menu.html.twig';

    public function __construct(
        private readonly MenuProvider $menus,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dgtx_menu', $this->render(...), ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('dgtx_menu_tree', $this->tree(...)),
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    public function render(Environment $twig, string|int $identifier, array $options = []): string
    {
        $tree = $this->tree($identifier, isset($options['locale']) ? (string) $options['locale'] : null);

        if (null === $tree || $tree->isEmpty()) {
            return '';
        }

        return $twig->render((string) ($options['template'] ?? self::TEMPLATE), [
            'tree' => $tree,
            'options' => $options,
        ]);
    }

    public function tree(string|int $identifier, ?string $locale = null): ?MenuTree
    {
        $tree = $this->menus->getTree($identifier, $locale);
        $tree?->markCurrent($this->requestStack->getCurrentRequest()?->getPathInfo() ?? '');

        return $tree;
    }
}
