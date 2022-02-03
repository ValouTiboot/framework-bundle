<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;

class AdminTranslationController extends AdminController
{
    /**
     * @return Response
     */
    public function read()
    {
        $tplVars = [];
        $fieldConfig = $this->get('dgtx.field.config');
        $fields = $this->get('dgtx.field.factory')->build($fieldConfig);
        $action = $this->generateUrl('dgtx_admin_entity_create', ['entityName' => $this->getContext()->getEntity()->getName()]);
        $form = $this->get('dgtx.form.factory')->buildForm($fields, ['method' => 'GET', 'action' => $action]);
        $helperForm = $this->get('dgtx.helper.form.factory')->build($fieldConfig, $form, $tplVars);

        return $this->display($helperForm->generateForm());
    }

    /**
     * @return Response
     */
    public function create(string $entityName)
    {
        $tplVars = [];
    	$data = $this->getContext()->getRequest()->query->get('translation');

    	$translationFactory = $this->get('dgtx.translation.form.factory')->build($data);
    	$translationForms = $translationFactory->buildFields()->buildForms();

        $helperForm = $this->get('dgtx.helper.form.factory')
        	->buildMulti($translationForms, $tplVars)
        	->setTemplate('@DigitixFramework/admin/helper/form/translation/create')
        ;

		foreach ($translationForms as $form) {
	        $errors = $form->getErrors(true, false);

	        if (count($errors) > 0) {
                $this->addFlash('danger', $errors);
            }

	        if ($form->isSubmitted() && $form->isValid()) {
	        	$data = $this->getContext()->getRequest()->query->get('translation');
	        	$orignalTranslations = $translationFactory->getProvider()->getTranslations();

                $updater = $this->get('dgtx.translation.updater');
	        	$updater->prepare($orignalTranslations, $form);
	        	$updater->write($data['locale']);

                $this->addFlash('success', $this->getContext()->trans('Translations successfuly updated.', [], 'Admin.Message.Success'));
	        }
		}

    	return $this->display($helperForm->generateForm());
    }
}
