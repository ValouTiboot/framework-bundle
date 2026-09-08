<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Admin\View\TemplateResolver;
use Digitix\FrameworkBundle\Factory\TranslationFormFactory;
use Digitix\FrameworkBundle\Updater\TranslationUpdater;
use Symfony\Component\HttpFoundation\Response;

/**
 * Translation editor.
 *
 *  read:   selection form (type, theme, locale), submitted in GET to create()
 *  create: one form per translation domain, written back to translations/
 */
final class AdminTranslationController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslationFormFactory::class,
            TranslationUpdater::class,
        ]);
    }

    public function read(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::READ, $context);

        $form = $this->forms()->createForm($context, [
            'method' => 'GET',
            'action' => $this->generateUrl('dgtx_admin_entity_create', ['entityName' => $context->getEntitySlug()]),
            'csrf_protection' => false,
        ]);

        // the entity "form.template" is the multi-form page used by create()
        return $this->renderAdmin(TemplateResolver::FORM, $this->formView()->build($context, $form));
    }

    public function create(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $request = $context->getRequest();
        $selection = $request->query->all('translation');

        if (empty($selection['type']) || empty($selection['locale'])) {
            return $this->redirectToList($context);
        }

        $factory = $this->container->get(TranslationFormFactory::class)->build($selection);
        $forms = $factory->buildForms($request);

        foreach ($forms as $form) {
            if ($form->isSubmitted() && $form->isValid()) {
                $updater = $this->container->get(TranslationUpdater::class);
                $updater->prepare($factory->getProvider()->getTranslations(), $form);
                $updater->write((string) $selection['locale']);

                $this->addFlash('success', $this->trans('Translations successfuly updated.'));

                return $this->redirect($request->getUri());
            }
        }

        return $this->renderAdmin(
            $this->templates()->form($context->getEntityConfig()),
            $this->formView()->buildMulti($context, $forms)
        );
    }
}
