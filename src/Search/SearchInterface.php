<?php

namespace Digitix\FrameworkBundle\Search;

use Digitix\FrameworkBundle\Filter\FilterInterface;
use Symfony\Component\Form\FormInterface;

interface SearchInterface
{
	public function prepareWhereClause(FilterInterface $filter, FormInterface $form);

	public function getNameParameter();

	public function getValueParameter();

	public function getQueryString();
}
