<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Entity\Configuration;
use Digitix\FrameworkBundle\Utils\Cache;
use Doctrine\Common\Collections\ArrayCollection;

class AdminPerformanceController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return [
            'dgtx.configuration.provider' => '?'.ConfigurationProvider::class,
            'dgtx.cache' => '?'.Cache::class,
        ] + parent::getSubscribedServices();
    }

	/**
     * {@inheritdoc}
     */
    public function create(string $entityName)
    {
        if ($this->getContext()->getRequest()->query->get('cacheClear') !== null) {
            if (($error = $this->get('dgtx.cache')->cacheClear()) != 0) {
                $this->addFlash('danger', $this->getContext()->trans('Something goes wrong when clearing cache: '.$error, [], 'Admin.Message.Error'));
            } else {
                $this->addFlash('success', $this->getContext()->trans('Clearing cache Ok', [], 'Admin.Message.Success'));
            }

            return $this->redirectToRoute($this->getContext()->getRequest()->attributes->get('_route'), $this->getContext()->getRequest()->attributes->get('_route_params'));
        }

        $datas = [];
        $tplVars = [];
        $fields = $this->get('dgtx.field.factory')->build();

        foreach ($fields as $name => $field) {
            $value = $this->getContext()->getConfiguration($name);
            $datas[$name] = $value;
        }

        $form = $this->get('dgtx.form.factory')->buildForm([], $datas);
        $helperForm = $this->get('dgtx.helper.form.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $form,
                $tplVars
            )
        ;

        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->preparePersistenEntities($form->getData());
            $this->addFlash(
                'success',
                $this->getContext()->trans('Succesfully updated.', [], 'Admin.Message.Success')
            );
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
