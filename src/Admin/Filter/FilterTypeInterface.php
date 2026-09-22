<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter;

use Digitix\FrameworkBundle\Admin\Config\FilterConfig;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * A list filter: renders a form input and narrows the list query.
 */
#[AutoconfigureTag(FilterTypeRegistry::TAG)]
interface FilterTypeInterface
{
    /** Name used in the YAML "type" key. */
    public static function getTypeName(): string;

    /** @return class-string<\Symfony\Component\Form\FormTypeInterface<mixed>> */
    public function getFormType(FilterConfig $filter): string;

    /** @return array<string, mixed> */
    public function getFormOptions(FilterConfig $filter): array;

    /**
     * @param string $alias DQL alias of the filtered property ("a" entity, "t" translation)
     * @param mixed  $value submitted, non-empty value
     */
    public function apply(QueryBuilder $queryBuilder, FilterConfig $filter, string $alias, mixed $value): void;
}
