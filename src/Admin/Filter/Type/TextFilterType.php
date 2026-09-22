<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter\Type;

use Digitix\FrameworkBundle\Admin\Config\FilterConfig;
use Digitix\FrameworkBundle\Admin\Filter\AbstractFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Contains" search on a string property.
 */
class TextFilterType extends AbstractFilterType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public static function getTypeName(): string
    {
        return 'text';
    }

    public function getFormType(FilterConfig $filter): string
    {
        return TextType::class;
    }

    public function apply(QueryBuilder $queryBuilder, FilterConfig $filter, string $alias, mixed $value): void
    {
        $parameter = $this->parameterName($filter);

        $queryBuilder
            ->andWhere(sprintf('%s.%s LIKE :%s', $alias, $filter->name, $parameter))
            ->setParameter($parameter, '%'.addcslashes((string) $value, '%_').'%');
    }

    protected function getTypeOptions(FilterConfig $filter): array
    {
        if (null === $filter->label) {
            return [];
        }

        return ['attr' => ['placeholder' => $this->translator->trans($filter->label, [], self::TRANSLATION_DOMAIN)]];
    }
}
