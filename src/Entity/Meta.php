<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SEO meta of a static front page, keyed by route name.
 */
#[ORM\Entity]
class Meta extends Translatable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $page = null;

    /** @var Collection<int, MetaTranslation> */
    #[ORM\OneToMany(targetEntity: MetaTranslation::class, mappedBy: 'translatable', cascade: ['all'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;

        return $this;
    }

    /** @return Collection<int, MetaTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(MetaTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(MetaTranslation $translation): self
    {
        if ($this->translations->removeElement($translation) && $translation->getTranslatable() === $this) {
            $translation->setTranslatable(null);
        }

        return $this;
    }
}
