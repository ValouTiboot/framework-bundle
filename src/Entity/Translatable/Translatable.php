<?php

namespace Digitix\FrameworkBundle\Entity\Translatable;

class Translatable
{
	public function __set($name, $value)
	{
		$translationFqcn = get_class($this).'Translation';

		if (preg_match('@(translatable)@', $name))
		{
			$method = 'set'.str_replace('translatable', '', $name);

			if (method_exists($translationFqcn, $method))
			{
				foreach ($this->getTranslations() as &$translation)
		        {
		            if (isset($value[$translation->getLanguage()->getId()]) || $value[$translation->getLanguage()->getId()] === null)
		                $translation->{$method}($value[$translation->getLanguage()->getId()]);
		        }
			}
		}

		return $this;
	}

	public function __get($name)
	{
		$translationFqcn = get_class($this).'Translation';

		if (preg_match('@(translatable)@', $name))
		{
			$method = 'get'.str_replace('translatable', '', $name);

			if (method_exists($translationFqcn, $method))
			{
				$getter = [];
		        $translations = $this->getTranslations();

		        foreach ($translations as $translation)
		            $getter[$translation->getLanguage()->getId()] = $translation->{$method}();

	        	return $getter;
        	}
		}
		else
		{
			$method = 'get'.ucfirst($name);

			if (method_exists($translationFqcn, $method))
			{
		        $translations = $this->getTranslations();

		        foreach ($translations as $translation)
		        if ($translation->getLanguage()->getId() == '1') // get the context lanaguage id
		        	return $translation->{$method}();
        	}
		}

		return null;
	}

	public function __call($method, $args)
	{
		$translationFqcn = get_class($this).'Translation';
		if (class_exists($translationFqcn))
		{
			if (substr($method, 0, 3) != 'get')
				$method = 'get'.ucfirst($method);

			if (method_exists($translationFqcn, $method))
			{
		        $translations = $this->getTranslations();

		        foreach ($translations as $translation)
		        if ($translation->getLanguage()->getId() == '1') // get the context lanaguage id
		        	return $translation->{$method}();
			}
		}
	}

}
