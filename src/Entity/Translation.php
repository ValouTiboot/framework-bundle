<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

/**
 * Not persisted: backs the selection form of the translation editor
 * (which scope, which theme, which locale).
 */
final class Translation
{
    private ?string $type = null;
    private ?Language $locale = null;
    private ?string $theme = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
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

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }
}
