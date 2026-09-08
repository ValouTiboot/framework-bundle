<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Utils\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Performance" virtual entity: same Configuration-backed form as
 * Parameter, plus a "clear cache" action (?cacheClear).
 */
final class AdminPerformanceController extends AdminParameterController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [Cache::class]);
    }

    public function create(AdminContext $context): Response
    {
        if ($context->getRequest()->query->has('cacheClear')) {
            $this->assertGranted(AdminPermission::EDIT, $context);

            $exitCode = $this->container->get(Cache::class)->cacheClear();

            if (0 !== $exitCode) {
                $this->addFlash('danger', $this->trans('Something goes wrong when clearing cache: %code%', ['%code%' => $exitCode], 'Admin.Message.Error'));
            } else {
                $this->addFlash('success', $this->trans('Clearing cache Ok', [], 'Admin.Message.Success'));
            }

            return $this->redirectToRoute('dgtx_admin_entity_create', ['entityName' => $context->getEntitySlug()]);
        }

        return parent::create($context);
    }
}
