<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class MenuItemTranslation
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: MenuItem::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MenuItem $translatable = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Language::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Language $language = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $label = null;

    public function getTranslatable(): ?MenuItem
    {
        return $this->translatable;
    }

    public function setTranslatable(?MenuItem $translatable): self
    {
        $this->translatable = $translatable;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
