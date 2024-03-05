<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Form\Type\FiltersFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class FilterFormFactory
{
	private $filterFactory;
	private $formFactory;
	private $context;

	public function __construct(
		ContextProvider $contextProvider,
		FormFactoryInterface $formFactory
	)
	{
		$this->context = $contextProvider->getContext();
		$this->formFactory = $formFactory;
	}

	public function buildForm(): FormInterface
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
}
