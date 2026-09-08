<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class MenuTranslation
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $translatable = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Language::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Language $language = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    public function getTranslatable(): ?Menu
    {
        return $this->translatable;
    }

    public function setTranslatable(?Menu $translatable): self
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
}
