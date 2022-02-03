<?php

namespace Digitix\FrameworkBundle\Utils;

final class ToolString
{
	/*
	* @TOOD
	*/
	public static function toCamelCase(string $string): string
	{
		$string = str_replace(['.',','], '', $string);

		return $string;
	}

	/**
	 * Transform a name to rewrite string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toNurl($string)
	{
		$string = htmlentities($string, ENT_NOQUOTES, 'utf-8');

		$string = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $string);
		$string = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $string);
		$string = preg_replace('#\&[^;]+\;#', '', $string);
		$string = preg_replace('([^a-zA-Z0-9-_])', '-', $string);
		while (strlen($string) != strlen(($string = str_replace('--', '-', $string))));

		return strtolower($string);
	}

	/**
	 * Transform a Camel to rewrite string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function camelToNurl($string)
	{
		return strtolower(substr($string, 0, 1)).substr($string, 1);
	}
}
