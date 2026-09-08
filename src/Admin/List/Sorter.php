<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\List;

use Digitix\FrameworkBundle\Admin\Config\ListConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Current sort state of a list, resolved from the list configuration
 * (default_sort) and the "sortBy" / "sortWay" query parameters.
 *
 * Only fields declared sortable in the configuration are accepted, so the
 * query string can never inject an arbitrary ORDER BY.
 */
final class Sorter
{
    public const ASC = 'asc';
    public const DESC = 'desc';

    private function __construct(
        private readonly string $fieldName,
        private readonly string $property,
        private readonly string $direction,
        private readonly Request $request,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $entitySlug,
    ) {
    }

    public static function fromRequest(Request $request, ListConfig $list, UrlGeneratorInterface $urlGenerator, string $entitySlug): self
    {
        $fieldName = 'id';
        $property = 'id';
        $direction = self::ASC;

        if (null !== ($default = $list->getDefaultSort())) {
            [$fieldName, $direction] = $default;
            $property = $list->fields[$fieldName]->property;
        }

        $requestedField = $request->query->get('sortBy');
        if (\is_string($requestedField) && null !== ($field = $list->getField($requestedField)) && $field->isSortable()) {
            $fieldName = $field->name;
            $property = $field->property;
            $direction = self::ASC;
        }

        $requestedDirection = strtolower((string) $request->query->get('sortWay', $direction));
        if (\in_array($requestedDirection, [self::ASC, self::DESC], true)) {
            $direction = $requestedDirection;
        }

        return new self($fieldName, $property, $direction, $request, $urlGenerator, $entitySlug);
    }

    /** Name of the sorted list field (as used in the template). */
    public function getOrderBy(): string
    {
        return $this->fieldName;
    }

    /** Entity property to ORDER BY. */
    public function getProperty(): string
    {
        return $this->property;
    }

    public function getOrderWay(): string
    {
        return $this->direction;
    }

    public function generateLink(string $fieldName, string $direction): string
    {
        $params = array_merge($this->request->query->all(), [
            'entityName' => $this->entitySlug,
            'sortBy' => $fieldName,
            'sortWay' => self::DESC === strtolower($direction) ? self::DESC : self::ASC,
        ]);

        return $this->urlGenerator->generate((string) $this->request->attributes->get('_route'), $params);
    }
}
