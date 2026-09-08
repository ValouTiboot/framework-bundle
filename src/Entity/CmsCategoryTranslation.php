<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CmsCategoryTranslation
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CmsCategory::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CmsCategory $translatable = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Language::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Language $language = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rewrite = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    public function getTranslatable(): ?CmsCategory
    {
        return $this->translatable;
    }

    public function setTranslatable(?CmsCategory $translatable): self
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

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getRewrite(): ?string
    {
        return $this->rewrite;
    }

    public function setRewrite(?string $rewrite): self
    {
        $this->rewrite = $rewrite;

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
