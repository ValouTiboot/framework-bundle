<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Entity\Configuration;
use Doctrine\Common\Collections\ArrayCollection;

class AdminPerformanceController extends AdminController
{
	/**
     * @return Response
     */
    public function create($entityName)
    {
        if ($this->getContext()->getRequest()->query->get('cacheClear') !== null)
        {
            if (($error = $this->get('dgtx.cache')->cacheClear()) != 0) {
                $this->addFlash('danger', $this->getContext()->trans('Something goes wrong when clearing cache: '.$error, [], 'Admin.Message.Error'));
            } else {
                $this->addFlash('success', $this->getContext()->trans('Clearing cache Ok', [], 'Admin.Message.Success'));
            }

            return $this->redirectToRoute($this->getContext()->getRequest()->attributes->get('_route'), $this->getContext()->getRequest()->attributes->get('_route_params'));
        }

        $tplVars = [];
        $datas = [];

        $configurationProvider = $this->get('dgtx.configuration.provider');
        $fieldConfig = $this->get('dgtx.field.config');
        $fields = $this->get('dgtx.field.factory')->build($fieldConfig);

        foreach ($fields as $name => $field) {
            $configuration = $configurationProvider->get($name);
            $datas[$name] = $configuration !== null ? $configuration->getValue() : null;
        }

        $form = $this->get('dgtx.form.factory')->buildForm($fields, ['data' => $datas]);
        $helperForm = $this->get('dgtx.helper.form.factory')->build($fieldConfig, $form, $tplVars);
        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->preparePersistenEntities($form->getData());
            $this->addFlash('success', $this->getContext()->trans('Succesfully updated.', [], 'Admin.Message.Success'));
        }

        return $this->display($helperForm->generateForm());
    }

    protected function preparePersistenEntities($data)
    {
        $objects = [];
        $configurationProvider = $this->get('dgtx.configuration.provider');

        foreach ($data as $propertie => $value) {
            if (($configuration = $configurationProvider->get($propertie)) === null) {
                $configuration = new Configuration();
                $configuration->setName($propertie);
            }

            $configuration->setValue($value === false ? 0 : $value);
            $objects[] = $configuration;
        }

        return $this->persistEntities(new ArrayCollection($objects, false));
    }
}
