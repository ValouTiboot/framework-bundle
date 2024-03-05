<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Factory\TranslationFormFactory;
use Digitix\FrameworkBundle\Updater\TranslationUpdater;

class AdminTranslationController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return [
            'dgtx.translation.form.factory' => '?'.TranslationFormFactory::class,
            'dgtx.translation.updater' => '?'.TranslationUpdater::class
        ] + parent::getSubscribedServices();
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $entityName)
    {
        $tplVars = [];

        $action = $this->generateUrl(
            'dgtx_admin_entity_create',
            [
                'entityName' => $this->getContext()->getEntity()->getName()
            ]
        );

        $form = $this->get('dgtx.form.factory')->buildForm(['method' => 'GET', 'action' => $action]);

        $helperForm = $this->get('dgtx.helper.form.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $form,
                $tplVars
            )
        ;

        return $this->display($helperForm
            ->setTemplateOverride('@DigitixFramework/admin/helper/form/create')
            ->generateForm()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $entityName)
    {
        $tplVars = [];
    	$data = $this->getContext()->getRequest()->query->all('translation');

    	$translationFactory = $this->get('dgtx.translation.form.factory')->build($data);
    	$translationForms = $translationFactory->buildFields()->buildForms();

        $helperForm = $this->get('dgtx.helper.form.factory')
        	->buildMulti(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $translationForms,
                $tplVars
            )
        ;

		foreach ($translationForms as $form) {
	        $errors = $form->getErrors(true, false);

	        if (count($errors) > 0) {
                $this->addFlash('danger', $errors);
            }

	        if ($form->isSubmitted() && $form->isValid()) {
	        	$orignalTranslations = $translationFactory->getProvider()->getTranslations();

                $updater = $this->get('dgtx.translation.updater');
	        	$updater->prepare($orignalTranslations, $form);
	        	$updater->write($data['locale']);

                $this->addFlash(
                    'success',
                    $this->getContext()->trans('Translations successfuly updated.', [], 'Admin.Message.Success')
                );
	        }
		}

    	return $this->display($helperForm->generateForm());
    }
}
