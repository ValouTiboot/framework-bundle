<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter\Type;

use Digitix\FrameworkBundle\Admin\Config\FilterConfig;
use Digitix\FrameworkBundle\Admin\Filter\AbstractFilterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class BoolFilterType extends AbstractFilterType
{
    public static function getTypeName(): string
    {
        return 'bool';
    }

    public function getFormType(FilterConfig $filter): string
    {
        return ChoiceType::class;
    }

    protected function getTypeOptions(FilterConfig $filter): array
    {
        return [
            'choices' => ['yes' => true, 'no' => false],
            'choice_label' => static fn ($choice, string $key): string => 'label.default.'.$key,
            'choice_translation_domain' => self::TRANSLATION_DOMAIN,
            'placeholder' => '',
        ];
    }
}
