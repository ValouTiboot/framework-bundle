<?php

namespace Digitix\FrameworkBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FiltersFormType extends AbstractType
{
	/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['filters'] as $filter) {
            $builder->add($filter->getName(), $filter->getFormType(), $filter->getFormFilterOptions());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('filters');
        $resolver->setAllowedTypes('filters', ArrayCollection::class);

        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'filters';
    }
}
