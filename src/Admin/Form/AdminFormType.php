<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Generic form whose children are given as definitions:
 *   [['name' => 'email', 'type' => EmailType::class, 'options' => [...]], ...]
 *
 * @extends AbstractType<mixed>
 */
final class AdminFormType extends AbstractType
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
                'allow_extra_fields' => true,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'dgtx_form';
    }
}
