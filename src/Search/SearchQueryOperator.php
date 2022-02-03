<?php

namespace Digitix\FrameworkBundle\Search;

class SearchQueryOperator
{
	const EQUAL = '=';
	const NOT_EQUAL = '!=';
	const INF_THAN = '<';
	const SUP_THAN = '>';
	const INF_EQUAL_TO = '<=';
	const SUP_EQUAL_TO = '>=';
	const CONTAINS = 'LIKE';
	const NOT_CONTAINS = 'NOT LIKE';
}
