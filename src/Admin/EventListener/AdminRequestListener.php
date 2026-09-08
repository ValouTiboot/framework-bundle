<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\EventListener;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Context\AdminContextFactory;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Runs on admin routes only (those carrying the "_dgtx_admin" default), right
 * after the router and before the locale listener (priority 16):
 *
 *  1. resolves the entity from the "entityName" route parameter (404 if unknown),
 *  2. points "_controller" to the controller configured for that entity,
 *  3. builds the AdminContext and stores it in the request,
 *  4. sets "_locale" from the default language so translations follow.
 */
#[AsEventListener(event: KernelEvents::REQUEST, priority: 17)]
final class AdminRequestListener
{
    public function __construct(
        private readonly AdminConfig $config,
        private readonly AdminContextFactory $contextFactory,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest() || !$request->attributes->get('_dgtx_admin')) {
            return;
        }

        $entityName = $request->attributes->get('entityName');

        if (!\is_string($entityName) || '' === $entityName) {
            return; // login, logout...
        }

        if (!$this->config->hasEntity($entityName)) {
            throw new NotFoundHttpException(sprintf('Unknown admin entity "%s".', $entityName));
        }

        $entityConfig = $this->config->getEntity($entityName);

        // Only the generic controller is swapped: a route resolved to something
        // else (e.g. the framework's trailing-slash RedirectController) is left alone.
        $controller = $request->attributes->get('_controller');
        if (\is_string($controller) && str_starts_with($controller, AdminController::class.'::')) {
            [, $action] = explode('::', $controller, 2);
            $request->attributes->set('_controller', $entityConfig->controller.'::'.$action);
        }

        $context = $this->contextFactory->create($entityConfig, $request);
        $request->attributes->set(AdminContext::ATTRIBUTE, $context);
        $request->attributes->set('_locale', $context->getLanguage()->getLocale());
    }
}
