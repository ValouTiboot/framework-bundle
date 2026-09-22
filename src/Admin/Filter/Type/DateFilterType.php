<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter\Type;

use Digitix\FrameworkBundle\Admin\Config\FilterConfig;
use Digitix\FrameworkBundle\Admin\Filter\AbstractFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * Matches every record of the selected day.
 */
final class DateFilterType extends AbstractFilterType
{
    public static function getTypeName(): string
    {
        return 'date';
    }

    public function getFormType(FilterConfig $filter): string
    {
        return DateType::class;
    }

    public function apply(QueryBuilder $queryBuilder, FilterConfig $filter, string $alias, mixed $value): void
    {
        if (!$value instanceof \DateTimeInterface) {
            return;
        }

        $parameter = $this->parameterName($filter);
        $from = \DateTimeImmutable::createFromInterface($value)->setTime(0, 0);

        $queryBuilder
            ->andWhere(sprintf('%1$s.%2$s >= :%3$s_from AND %1$s.%2$s < :%3$s_to', $alias, $filter->name, $parameter))
            ->setParameter($parameter.'_from', $from)
            ->setParameter($parameter.'_to', $from->modify('+1 day'));
    }

    protected function getTypeOptions(FilterConfig $filter): array
    {
        return ['widget' => 'single_text'];
    }
}
