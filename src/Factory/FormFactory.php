<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Form\Type\FiltersFormType;
use Digitix\FrameworkBundle\Form\Type\FormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class FormFactory
{
	private $formFactory;
	private $context;
	private $filterFactory;

	public function __construct(ContextProvider $contextProvider, FilterFactory $filterFactory, FormFactoryInterface $formFactory)
	{
		$this->context = $contextProvider->getContext();
		$this->filterFactory = $filterFactory;
		$this->formFactory = $formFactory;
	}

	public function buildFormFilters(): FormInterface
	{
        $filtersForm = $this->formFactory->createNamed(
			'filters',
			FiltersFormType::class,
			$this->context->getEntity()->getInstance(),
			[
            	'method' => 'GET',
            	'filters' => $this->filterFactory->build(),
			]
		);

        return $filtersForm->handleRequest($this->context->getRequest());
	}

	public function buildForm(ArrayCollection $fields, $overrideOptions = [], $data = null): FormInterface
	{
		$options = [
            'method' => 'POST',
            'fields' => $fields,
            'allow_file_upload' => true,
        ];

        if (is_null($data) && !is_null($this->context->getEntity()) && is_object($this->context->getEntity()->getInstance()))
        	$data = $this->context->getEntity()->getInstance();

		$form = $this->formFactory->createNamed(
			strtolower($this->context->getEntityName()),
			FormType::class,
			$data,
			array_merge($options, $overrideOptions)
		);

        return $form->handleRequest($this->context->getRequest());
	}
}
