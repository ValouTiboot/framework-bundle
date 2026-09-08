<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * List filters: a GET form, never bound to an entity, without CSRF.
 */
final class AdminFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['fields'] as $definition) {
            $builder->add($definition['name'], $definition['type'], $definition['options'] ?? []);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('fields')
            ->setAllowedTypes('fields', 'array')
            ->setDefaults([
                'method' => 'GET',
                'csrf_protection' => false,
                'allow_extra_fields' => true,
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'filters';
    }
}
