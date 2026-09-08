<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\List\Paginator;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationStatus;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Translation editor.
 *
 *  read:           one locale (tab), filters (domain, status, search), paginated table
 *  update action:  POST {value, _token} for one entry, JSON when requested by script
 *  refresh action: POST, re-extracts the keys from the code
 *  edit:           generic form, fallback without JavaScript
 *
 * Every save regenerates the catalogue files of the locale.
 */
class AdminTranslationController extends AdminController
{
    public const CSRF_TOKEN_ID = 'dgtx_translation';

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            LanguageProvider::class,
            TranslationRepository::class,
            TranslationCompiler::class,
            TranslationSynchronizer::class,
        ]);
    }

    public function read(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::READ, $context);

        $request = $context->getRequest();
        $languages = $this->languages()->getActiveLanguages();
        $locale = $this->localeFromRequest($request, $languages, $context);

        $filters = [
            'domain' => trim((string) $request->query->get('domain', '')),
            'status' => trim((string) $request->query->get('status', '')),
            'q' => trim((string) $request->query->get('q', '')),
        ];

        $paginator = new Paginator(
            $this->repository()->createListQueryBuilder($locale, $filters['domain'], TranslationStatus::tryFrom($filters['status']), $filters['q']),
            Paginator::pageFromRequest($request),
            $context->getEntityConfig()->list->itemsPerPage,
            $request,
            $this->container->get('router'),
            $context->getEntitySlug(),
        );

        return $this->renderAdmin($this->templates()->list($context->getEntityConfig()), $this->baseVars($context) + [
            'languages' => $languages,
            'locale' => $locale,
            'counts' => $this->counts(),
            'domains' => $this->repository()->findDomains(),
            'statuses' => TranslationStatus::values(),
            'filters' => $filters,
            'entities' => $paginator->getResults(),
            'pagination' => $paginator,
            'total_items' => $paginator->getTotal(),
            'can_edit' => $this->isGranted(AdminPermission::EDIT, $context->getEntityConfig()),
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken(self::CSRF_TOKEN_ID)->getValue(),
        ]);
    }

    /**
     * Inline edition: POST {value, _token} (JSON body or form fields).
     */
    public function updateAction(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $request = $context->getRequest();
        $payload = $this->payload($request);
        $wantsJson = $request->isXmlHttpRequest() || 'json' === $request->getContentTypeFormat();

        if (!$request->isMethod('POST') || !$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, (string) ($payload['_token'] ?? ''))) {
            return $this->failure($context, $wantsJson, $this->trans('Invalid security token, please try again.', [], 'Admin.Message.Error'), Response::HTTP_FORBIDDEN);
        }

        $entry = $context->getEntity();
        if (!$entry instanceof Translation || null === $entry->getId()) {
            throw new NotFoundHttpException('Translation entry not found.');
        }

        $value = $payload['value'] ?? null;
        $entry->setValue(\is_scalar($value) ? (string) $value : null);
        $this->persister()->save($entry, false);
        $this->compiler()->compile((string) $entry->getLocale());

        if ($wantsJson) {
            return new JsonResponse([
                'id' => $entry->getId(),
                'locale' => $entry->getLocale(),
                'value' => $entry->getValue(),
                'status' => $entry->getStatusValue(),
                'statusLabel' => $this->trans('label.translation.'.$entry->getStatusValue(), [], 'Admin.Fields.Label'),
                'counts' => $this->counts()[$entry->getLocale()] ?? ['total' => 0, 'translated' => 0, 'obsolete' => 0],
            ]);
        }

        $this->addFlash('success', $this->trans('Entity successfuly updated.', [], 'Admin.Message.Success'));

        return $this->redirectToEditor($context, (string) $entry->getLocale());
    }

    /**
     * Re-extracts the keys from the code (POST + token), then compiles.
     */
    public function refreshAction(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $request = $context->getRequest();
        $locale = (string) $request->query->get('locale', '');

        if (!$request->isMethod('POST') || !$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', $this->trans('Invalid security token, please try again.', [], 'Admin.Message.Error'));

            return $this->redirectToEditor($context, $locale);
        }

        $report = $this->container->get(TranslationSynchronizer::class)->synchronize();
        $this->compiler()->compile();

        $this->addFlash('success', $this->trans(
            'Translation keys refreshed: %added% added, %obsoleted% obsolete.',
            ['%added%' => $report->added, '%obsoleted%' => $report->obsoleted],
            'Admin.Message.Success'
        ));

        return $this->redirectToEditor($context, $locale);
    }

    /**
     * Generic form (fallback without JavaScript): compiles after a save.
     */
    public function edit(AdminContext $context): Response
    {
        $response = parent::edit($context);

        if ($response instanceof RedirectResponse && ($entry = $context->getEntity()) instanceof Translation) {
            $this->compiler()->compile((string) $entry->getLocale());

            return $this->redirectToEditor($context, (string) $entry->getLocale());
        }

        return $response;
    }

    // --- helpers ---------------------------------------------------------------------

    /**
     * @param Language[] $languages
     */
    private function localeFromRequest(Request $request, array $languages, AdminContext $context): string
    {
        $requested = (string) $request->query->get('locale', '');

        foreach ($languages as $language) {
            if ($language->getLocale() === $requested) {
                return $requested;
            }
        }

        return (string) $context->getLanguage()->getLocale();
    }

    /**
     * Per locale: total (active keys), translated, obsolete.
     *
     * @return array<string, array{total: int, translated: int, obsolete: int}>
     */
    private function counts(): array
    {
        $counts = [];

        foreach ($this->repository()->countByLocaleAndStatus() as $locale => $byStatus) {
            $translated = $byStatus[TranslationStatus::Translated->value] ?? 0;
            $missing = $byStatus[TranslationStatus::Missing->value] ?? 0;

            $counts[$locale] = [
                'total' => $translated + $missing,
                'translated' => $translated,
                'obsolete' => $byStatus[TranslationStatus::Obsolete->value] ?? 0,
            ];
        }

        return $counts;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request): array
    {
        if ('json' === $request->getContentTypeFormat()) {
            $decoded = json_decode((string) $request->getContent(), true);

            return \is_array($decoded) ? $decoded : [];
        }

        return $request->request->all();
    }

    private function failure(AdminContext $context, bool $wantsJson, string $message, int $status): Response
    {
        if ($wantsJson) {
            return new JsonResponse(['error' => $message], $status);
        }

        $this->addFlash('danger', $message);

        return $this->redirectToEditor($context, (string) $context->getRequest()->query->get('locale', ''));
    }

    private function redirectToEditor(AdminContext $context, string $locale): RedirectResponse
    {
        $parameters = ['entityName' => $context->getEntitySlug()];
        if ('' !== $locale) {
            $parameters['locale'] = $locale;
        }

        return $this->redirectToRoute('dgtx_admin_entity_read', $parameters);
    }

    private function languages(): LanguageProvider
    {
        return $this->container->get(LanguageProvider::class);
    }

    private function repository(): TranslationRepository
    {
        return $this->container->get(TranslationRepository::class);
    }

    private function compiler(): TranslationCompiler
    {
        return $this->container->get(TranslationCompiler::class);
    }
}
