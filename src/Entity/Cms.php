<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Translated fields, resolved in the current language (see Translatable):
 *
 * @method string|null getName()
 * @method string|null getContent()
 * @method string|null getMetaTitle()
 * @method string|null getMetaDescription()
 * @method string|null getRewrite()
 */
#[ORM\Entity]
class Cms extends Translatable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CmsCategory::class)]
    private ?CmsCategory $category = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateUpd = null;

    /** @var Collection<int, CmsTranslation> */
    #[ORM\OneToMany(targetEntity: CmsTranslation::class, mappedBy: 'translatable', cascade: ['all'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?CmsCategory
    {
        return $this->category;
    }

    public function setCategory(?CmsCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateUpd(): ?\DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /** @return Collection<int, CmsTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(CmsTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(CmsTranslation $translation): self
    {
        if ($this->translations->removeElement($translation) && $translation->getTranslatable() === $this) {
            $translation->setTranslatable(null);
        }

        return $this;
    }
}
