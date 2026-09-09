<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

/**
 * A menu ready to be rendered: its identity and its root nodes.
 */
final class MenuTree
{
    /**
     * @param MenuNode[] $items root nodes
     */
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $name,
        public readonly string $locale,
        public readonly array $items,
    ) {
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    /**
     * Flags the node whose URL path is the current one, and its ancestors.
     */
    public function markCurrent(string $currentPath): void
    {
        $currentPath = rtrim($currentPath, '/') ?: '/';

        foreach ($this->items as $node) {
            self::mark($node, $currentPath);
        }
    }

    private static function mark(MenuNode $node, string $currentPath): bool
    {
        // only site-relative URLs can match: an absolute URL points elsewhere
        $path = null === parse_url($node->url, \PHP_URL_HOST) ? (string) (parse_url($node->url, \PHP_URL_PATH) ?? '') : '';
        $node->current = ('' !== $path) && (rtrim($path, '/') ?: '/') === $currentPath;

        foreach ($node->children as $child) {
            if (self::mark($child, $currentPath)) {
                $node->active = true;
            }
        }

        return $node->current || $node->active;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'locale' => $this->locale,
            'items' => array_map(static fn (MenuNode $node) => $node->toArray(), $this->items),
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            (string) $data['code'],
            (string) $data['name'],
            (string) $data['locale'],
            array_map(static fn (array $node) => MenuNode::fromArray($node), $data['items'] ?? []),
        );
    }
}
