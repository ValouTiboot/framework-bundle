<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\View;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Form\AdminFormBuilder;
use Digitix\FrameworkBundle\Admin\List\ListQueryBuilder;
use Digitix\FrameworkBundle\Admin\List\Paginator;
use Digitix\FrameworkBundle\Admin\List\Sorter;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Template variables of a list page.
 */
final class ListViewBuilder
{
    public const ACTIONS = ['view', 'view_entity', 'edit', 'delete'];
    public const TOOLBAR = ['add', 'backup'];

    public function __construct(
        private readonly AdminFormBuilder $forms,
        private readonly ListQueryBuilder $queries,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * @param array<string, mixed> $extra variables merged on top of the defaults
     *
     * @return array<string, mixed>
     */
    public function build(AdminContext $context, array $extra = []): array
    {
        $config = $context->getEntityConfig();
        $list = $config->list;
        $request = $context->getRequest();
        $slug = $context->getEntitySlug();

        $sorter = Sorter::fromRequest($request, $list, $this->urlGenerator, $slug);
        $filtersForm = $this->forms->createFiltersForm($context);

        $paginator = null;
        if (!$config->isVirtual()) {
            $paginator = new Paginator(
                $this->queries->create($context, $sorter, $filtersForm),
                Paginator::pageFromRequest($request),
                $list->itemsPerPage,
                $request,
                $this->urlGenerator,
                $slug,
            );
        }

        $actions = array_values(array_filter(
            $list->actions,
            fn (string $action) => \in_array($action, self::ACTIONS, true)
                && $this->authorizationChecker->isGranted(AdminPermission::forAction($action), $config)
        ));

        return array_replace([
            'controllerName' => $config->name,
            'entityName' => $slug,
            'entities' => $paginator?->getResults() ?? [],
            'sorter' => $sorter,
            'actions' => $actions,
            'toolbar' => array_values(array_intersect($list->toolbar, self::TOOLBAR)),
            'list_fields' => $list->fields,
            'pagination' => $paginator,
            'filters_form' => $filtersForm->createView(),
            'total_items' => $paginator?->getTotal() ?? 0,
            'has_create' => $list->hasCreate && $this->authorizationChecker->isGranted(AdminPermission::CREATE, $config),
            'header_link' => $list->headerLinks,
            'sortable' => $list->sortable,
        ], $extra);
    }
}
