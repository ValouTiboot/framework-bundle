<?php

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Entity\Language;

final class Translation
{
	/**
     * @var string
     */
	private $type;

	/**
     * @var object|null
     */
	private $locale;

	/**
     * @var string
     */
	private $theme;

    public function getType(): ?string
    {
    	return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocale(): ?Language
    {
    	return $this->locale;
    }

    public function setLocale(?Language $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTheme(): ?string
    {
    	return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }
}
