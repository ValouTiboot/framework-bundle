<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Utils\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Performance" virtual entity: same Configuration-backed form as
 * Parameter, plus the "clear cache" action (POST cacheClear=1 + token),
 * used by the page and by the button of the top bar.
 */
final class AdminPerformanceController extends AdminParameterController
{
    public const CACHE_CLEAR_TOKEN_ID = 'dgtx_cache_clear';

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [Cache::class]);
    }

    public function create(AdminContext $context): Response
    {
        $request = $context->getRequest();

        if ($request->isMethod('POST') && $request->request->has('cacheClear')) {
            $this->assertGranted(AdminPermission::EDIT, $context);

            if (!$this->isCsrfTokenValid(self::CACHE_CLEAR_TOKEN_ID, (string) $request->request->get('_token'))) {
                $this->addFlash('danger', $this->trans('Invalid security token, please try again.', [], 'Admin.Message.Error'));
            } elseif (0 !== ($exitCode = $this->container->get(Cache::class)->cacheClear())) {
                $this->addFlash('danger', $this->trans('Something goes wrong when clearing cache: %code%', ['%code%' => $exitCode], 'Admin.Message.Error'));
            } else {
                $this->addFlash('success', $this->trans('Clearing cache Ok', [], 'Admin.Message.Success'));
            }

            // back to the page the button was on (top bar), or to the performance page
            $referer = (string) $request->headers->get('referer', '');
            $target = str_starts_with($referer, $request->getSchemeAndHttpHost().'/')
                ? $referer
                : $this->generateUrl('dgtx_admin_entity_create', ['entityName' => $context->getEntitySlug()]);

            return $this->redirect($target);
        }

        return parent::create($context);
    }
}
