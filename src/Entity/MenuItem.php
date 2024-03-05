<?php

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MenuItem extends Translatable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="menuItems", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $menu;

    /**
     * @ORM\ManyToOne(targetEntity=MenuItem::class, inversedBy="parents", cascade={"persist"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=MenuItem::class, mappedBy="idParent")
     */
    private $parents;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idEntity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $route;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cssClass;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpd;

    /**
     * @ORM\OneToMany(targetEntity=MenuItemTranslation::class, mappedBy="translatable", cascade={"ALL"}, orphanRemoval=true)
     */
    private $translations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="integer")
     */
    private $depth;

    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(self $parent): self
    {
        if (!$this->parents->contains($parent)) {
            $this->parents[] = $parent;
            $parent->setParent($this);
        }

        return $this;
    }

    public function removeParent(self $parent): self
    {
        if ($this->parents->removeElement($parent)) {
            // set the owning side to null (unless already changed)
            if ($parent->getParent() === $this) {
                $parent->setParent(null);
            }
        }

        return $this;
    }

    public function getIdEntity(): ?int
    {
        return $this->idEntity;
    }

    public function setIdEntity(?int $idEntity): self
    {
        $this->idEntity = $idEntity;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getCssClass(): ?string
    {
        return $this->cssClass;
    }

    public function setCssClass(?string $cssClass): self
    {
        $this->cssClass = $cssClass;

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

    /**
     * @return Collection<int, MenuItemTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(MenuItemTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(MenuItemTranslation $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getTranslatable() === $this) {
                $translation->setTranslatable(null);
            }
        }

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }
}
