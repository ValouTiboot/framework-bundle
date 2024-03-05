<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Entity\Configuration;
use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Doctrine\Common\Collections\ArrayCollection;

class AdminParameterController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return [
            'dgtx.configuration.provider' => '?'.ConfigurationProvider::class,
        ] + parent::getSubscribedServices();
    }

	/**
     * {@inheritdoc}
     */
    public function create(string $entityName)
    {
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
                $this->getContext()->trans('Entity successfuly added.', [], 'Admin.Message.Success')
            );
        }

        return $this->display($helperForm->generateForm());
    }

    protected function preparePersistenEntities($data)
    {
        $entities = [];
        $configProvider = $this->get('dgtx.configuration.provider');

        foreach ($data as $propertie => $value) {
            if (($configuration = $configProvider->get($propertie)) === null) {
                $configuration = new Configuration();
                $configuration->setName($propertie);
            }

            $configuration->setValue($value === false ? 0 : $value);
            $entities[] = $configuration;
        }

        return $this->persistEntities(new ArrayCollection($entities, false));
    }
}
