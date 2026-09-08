<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Translation entries: the generic list and form, plus the catalogue
 * regeneration after every save and a "refresh keys" action.
 */
class AdminTranslationController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslationCompiler::class,
            TranslationSynchronizer::class,
        ]);
    }

    /**
     * "?refresh" re-extracts the keys from the code before listing.
     */
    public function read(AdminContext $context): Response
    {
        if ($context->getRequest()->query->has('refresh')) {
            $this->assertGranted(AdminPermission::EDIT, $context);

            $report = $this->container->get(TranslationSynchronizer::class)->synchronize();
            $this->container->get(TranslationCompiler::class)->compile();

            $this->addFlash('success', $this->trans(
                'Translation keys refreshed: %added% added, %obsoleted% obsolete.',
                ['%added%' => $report->added, '%obsoleted%' => $report->obsoleted],
                'Admin.Message.Success'
            ));

            return $this->redirectToList($context);
        }

        return parent::read($context);
    }

    public function edit(AdminContext $context): Response
    {
        $response = parent::edit($context);

        // saved: regenerate the catalogue of that locale
        if ($response instanceof RedirectResponse && ($entity = $context->getEntity()) instanceof Translation) {
            $this->container->get(TranslationCompiler::class)->compile($entity->getLocale());
        }

        return $response;
    }
}
