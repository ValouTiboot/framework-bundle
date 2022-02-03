<?php

namespace Digitix\FrameworkBundle\Provider;

use Symfony\Component\Translation\Loader\ArrayLoader;

final class TranslationProvider
{
	private $translations;

	public function getTradInFile(?array $files = [], string $locale)
	{
		$translations = [];
		foreach ($files as $file) {
			$loader = new ArrayLoader();
			$resource = require_once($file);
			$domain = str_replace('.'.$locale, '', pathinfo($file, PATHINFO_FILENAME));

			$catalog = $loader->load($resource, $locale, $domain);
			$translations[$domain] = $catalog->all($domain);
		}

		return $translations;
	}

	public function setTranslations(array $translations = []): self
	{
		$this->translations = $translations;
		return $this;
	}

	public function getTranslations()
	{
		return $this->translations;
	}
}
