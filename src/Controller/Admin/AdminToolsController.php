<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;
use Digitix\FrameworkBundle\Utils\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * "Tools" virtual entity: maintenance actions (cache, translations) and a
 * few facts about the runtime. Every action is a POST with the page token;
 * the "clear cache" button of the top bar posts here too.
 */
class AdminToolsController extends AdminController
{
    public const CSRF_TOKEN_ID = 'dgtx_tools';

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            Cache::class,
            TranslationSynchronizer::class,
            TranslationCompiler::class,
        ]);
    }

    public function view(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::VIEW, $context);

        return $this->renderAdmin($this->templates()->view($context->getEntityConfig()), $this->baseVars($context) + [
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken(self::CSRF_TOKEN_ID)->getValue(),
            'system' => [
                'php' => \PHP_VERSION,
                'symfony' => Kernel::VERSION,
                'environment' => (string) $this->getParameter('kernel.environment'),
                'debug' => (bool) $this->getParameter('kernel.debug'),
                'cache_dir' => (string) $this->getParameter('kernel.cache_dir'),
                'translations_dir' => $this->container->get(TranslationCompiler::class)->getOutputDir(),
            ],
        ]);
    }

    public function cacheClearAction(AdminContext $context): RedirectResponse
    {
        if (!$this->guard($context)) {
            return $this->back($context);
        }

        $exitCode = $this->container->get(Cache::class)->cacheClear();

        if (0 !== $exitCode) {
            $this->addFlash('danger', $this->trans('Something goes wrong when clearing cache: %code%', ['%code%' => $exitCode], 'Admin.Message.Error'));
        } else {
            $this->addFlash('success', $this->trans('Clearing cache Ok', [], 'Admin.Message.Success'));
        }

        return $this->back($context);
    }

    public function translationsExtractAction(AdminContext $context): RedirectResponse
    {
        if (!$this->guard($context)) {
            return $this->back($context);
        }

        $report = $this->container->get(TranslationSynchronizer::class)->synchronize();
        $this->container->get(TranslationCompiler::class)->compile();

        $this->addFlash('success', $this->trans(
            'Translation keys refreshed: %added% added, %obsoleted% obsolete.',
            ['%added%' => $report->added, '%obsoleted%' => $report->obsoleted],
            'Admin.Message.Success'
        ));

        return $this->back($context);
    }

    public function translationsCompileAction(AdminContext $context): RedirectResponse
    {
        if (!$this->guard($context)) {
            return $this->back($context);
        }

        $files = $this->container->get(TranslationCompiler::class)->compile();
        $this->addFlash('success', $this->trans('Translation catalogues compiled: %count% file(s).', ['%count%' => \count($files)], 'Admin.Message.Success'));

        return $this->back($context);
    }

    /** POST with a valid token, and the right to edit the tools; a flash explains a refusal. */
    private function guard(AdminContext $context): bool
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $request = $context->getRequest();
        if ($request->isMethod('POST') && $this->isCsrfTokenValid(self::CSRF_TOKEN_ID, (string) $request->request->get('_token'))) {
            return true;
        }

        $this->addFlash('danger', $this->trans('Invalid security token, please try again.', [], 'Admin.Message.Error'));

        return false;
    }

    /** Back to the page the button was on (top bar), or to the tools page. */
    private function back(AdminContext $context): RedirectResponse
    {
        $request = $context->getRequest();
        $referer = (string) $request->headers->get('referer', '');

        if (str_starts_with($referer, $request->getSchemeAndHttpHost().'/')) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('dgtx_admin_entity_view', ['entityName' => $context->getEntitySlug()]);
    }
}
