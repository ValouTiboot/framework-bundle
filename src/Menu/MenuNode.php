<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

/**
 * One rendered entry of a menu: resolved URL, title in the requested
 * language, children. Plain data, safe to cache as an array.
 */
final class MenuNode
{
    /** The node matches the current page. */
    public bool $current = false;

    /** One of the descendants matches the current page. */
    public bool $active = false;

    /**
     * @param MenuNode[] $children
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $target,
        public readonly ?string $cssClass,
        public readonly MenuItemType $type,
        public readonly int $depth,
        public array $children = [],
    ) {
    }

    public function hasChildren(): bool
    {
        return [] !== $this->children;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'target' => $this->target,
            'cssClass' => $this->cssClass,
            'type' => $this->type->value,
            'depth' => $this->depth,
            'children' => array_map(static fn (self $child) => $child->toArray(), $this->children),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            (string) $data['title'],
            (string) $data['url'],
            isset($data['target']) ? (string) $data['target'] : null,
            isset($data['cssClass']) ? (string) $data['cssClass'] : null,
            MenuItemType::from((string) $data['type']),
            (int) $data['depth'],
            array_map(static fn (array $child) => self::fromArray($child), $data['children'] ?? []),
        );
    }
}
