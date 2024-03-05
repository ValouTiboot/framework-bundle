<?php

namespace Digitix\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MetaTranslation
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Meta::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $translatable;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Language::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rewrite;

    public function getTranslatable(): ?Meta
    {
        return $this->translatable;
    }

    public function setTranslatable(?Meta $translatable): self
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

    public function setMetaTitle(string $metaTitle): self
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
}
