<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter;

use Digitix\FrameworkBundle\Admin\Config\FilterConfig;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractFilterType implements FilterTypeInterface
{
    public const TRANSLATION_DOMAIN = 'Admin.Fields.Label';

    public function getFormOptions(FilterConfig $filter): array
    {
        $options = [
            'required' => false,
            'translation_domain' => self::TRANSLATION_DOMAIN,
        ];

        if (null !== $filter->label) {
            $options['label'] = $filter->label;
        }

        return array_replace($options, $this->getTypeOptions($filter));
    }

    /** Default behaviour: strict equality. */
    public function apply(QueryBuilder $queryBuilder, FilterConfig $filter, string $alias, mixed $value): void
    {
        $parameter = $this->parameterName($filter);

        $queryBuilder
            ->andWhere(sprintf('%s.%s = :%s', $alias, $filter->name, $parameter))
            ->setParameter($parameter, $value);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTypeOptions(FilterConfig $filter): array
    {
        return [];
    }

    protected function parameterName(FilterConfig $filter): string
    {
        return 'dgtx_filter_'.preg_replace('/\W/', '_', $filter->name);
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveChoices(FilterConfig $filter): array
    {
        if ($filter->has('callback')) {
            $callback = $filter->get('callback');

            if (!\is_callable($callback)) {
                throw new \InvalidArgumentException(sprintf('Filter "%s": callback "%s" is not callable.', $filter->name, \is_string($callback) ? $callback : get_debug_type($callback)));
            }

            return (array) $callback();
        }

        $choices = $filter->get('choice', []);

        if (!\is_array($choices)) {
            throw new \InvalidArgumentException(sprintf('Filter "%s": "choice" must be a map of label => value, %s given.', $filter->name, get_debug_type($choices)));
        }

        return $choices;
    }
}
