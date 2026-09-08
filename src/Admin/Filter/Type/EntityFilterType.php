<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter\Type;

use Digitix\FrameworkBundle\Admin\Config\FilterConfig;
use Digitix\FrameworkBundle\Admin\Filter\AbstractFilterType;
use Digitix\FrameworkBundle\Admin\Persistence\EntityClassResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class EntityFilterType extends AbstractFilterType
{
    public function __construct(private readonly EntityClassResolver $classResolver)
    {
    }

    public static function getTypeName(): string
    {
        return 'entity';
    }

    public function getFormType(FilterConfig $filter): string
    {
        return EntityType::class;
    }

    protected function getTypeOptions(FilterConfig $filter): array
    {
        $collection = (array) $filter->get('collection', []);

        return [
            'class' => $this->classResolver->resolve((string) ($collection['name'] ?? $filter->name)),
            'choice_label' => (string) ($collection['label'] ?? 'name'),
            'choice_value' => (string) ($collection['value'] ?? 'id'),
            'placeholder' => '',
        ];
    }
}
