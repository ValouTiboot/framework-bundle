<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Digitix\FrameworkBundle\Menu\MenuItemType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * One entry of a Menu: a project route, a CMS page or a free link, placed in
 * a tree (parent + position) whose depth is bounded by the configuration.
 *
 * Translated fields, resolved in the current language (see Translatable):
 *
 * @method string|null getName()  title displayed in the menu
 * @method string|null getLabel() label of the source the item was created from
 */
#[ORM\Entity]
#[ORM\Index(name: 'idx_menu_item_order', columns: ['menu_id', 'parent_id', 'position'])]
class MenuItem extends Translatable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'menuItems', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?self $parent = null;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $children;

    /** Order among the siblings, from 0. */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $position = 0;

    /** 0 for a root item. Denormalised from the parent chain. */
    #[ORM\Column(type: 'integer')]
    private int $depth = 0;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    /** Id of the CMS page for MenuItemType::Cms. */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $idEntity = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $route = null;

    /** @var array<string, mixed>|null */
    #[ORM\Column(name: 'route_params', type: 'json', nullable: true)]
    private ?array $routeParams = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $link = null;

    /** "_blank" to open in a new tab. */
    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $target = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cssClass = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateUpd = null;

    /** @var Collection<int, MenuItemTranslation> */
    #[ORM\OneToMany(targetEntity: MenuItemTranslation::class, mappedBy: 'translatable', cascade: ['all'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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

    /** @return Collection<int, self> */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child) && $child->getParent() === $this) {
            $child->setParent(null);
        }

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
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
        $this->route = '' === $route ? null : $route;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getRouteParams(): array
    {
        return $this->routeParams ?? [];
    }

    /** @param array<string, mixed>|null $routeParams */
    public function setRouteParams(?array $routeParams): self
    {
        $this->routeParams = [] === $routeParams ? null : $routeParams;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = '' === $link ? null : $link;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): self
    {
        $this->target = '' === $target ? null : $target;

        return $this;
    }

    public function getCssClass(): ?string
    {
        return $this->cssClass;
    }

    public function setCssClass(?string $cssClass): self
    {
        $this->cssClass = '' === $cssClass ? null : $cssClass;

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

    /** @return Collection<int, MenuItemTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(MenuItemTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(MenuItemTranslation $translation): self
    {
        if ($this->translations->removeElement($translation) && $translation->getTranslatable() === $this) {
            $translation->setTranslatable(null);
        }

        return $this;
    }

    /** Translation for a given language id, if any (no fallback). */
    public function findTranslation(int $languageId): ?MenuItemTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->getLanguage()?->getId() === $languageId) {
                return $translation;
            }
        }

        return null;
    }

    /** Deduced from the stored fields: a link, a CMS page, or a route. */
    public function getType(): MenuItemType
    {
        if (null !== $this->link) {
            return MenuItemType::Link;
        }

        if (MenuItemType::CMS_ROUTE === $this->route && null !== $this->idEntity) {
            return MenuItemType::Cms;
        }

        return MenuItemType::Route;
    }
}
