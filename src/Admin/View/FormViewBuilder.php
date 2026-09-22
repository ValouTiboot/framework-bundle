<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\View;

use Digitix\FrameworkBundle\Admin\Config\FormConfig;
use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Template variables of a form page.
 */
final class FormViewBuilder
{
    public function __construct(private readonly PropertyAccessorInterface $propertyAccessor)
    {
    }

    /**
     * @param FormInterface<mixed> $form
     * @param array<string, mixed> $extra variables merged on top of the defaults
     *
     * @return array<string, mixed>
     */
    public function build(AdminContext $context, FormInterface $form, array $extra = [], ?FormConfig $formConfig = null): array
    {
        $formConfig ??= $context->getEntityConfig()->form;
        $uploadFields = $formConfig->getFieldsOfType('file');

        $uploadValues = [];
        $entity = $context->getEntity();
        foreach ($uploadFields as $field) {
            $uploadValues[$field->name] = null !== $entity && $this->propertyAccessor->isReadable($entity, $field->property)
                ? $this->propertyAccessor->getValue($entity, $field->property)
                : null;
        }

        return array_replace($this->common($context, $formConfig), [
            'fields' => $formConfig->fields,
            'uploadfields' => array_keys($uploadFields),
            'upload_values' => $uploadValues,
            'form' => $form->createView(),
            'forms' => [],
        ], $extra);
    }

    /**
     * Several independent forms on one page (translation editor).
     *
     * @param array<string, FormInterface<mixed>> $forms
     * @param array<string, mixed>                $extra
     *
     * @return array<string, mixed>
     */
    public function buildMulti(AdminContext $context, array $forms, array $extra = [], ?FormConfig $formConfig = null): array
    {
        $formConfig ??= $context->getEntityConfig()->form;

        return array_replace($this->common($context, $formConfig), [
            'fields' => [],
            'uploadfields' => [],
            'upload_values' => [],
            'form' => null,
            'forms' => array_map(static fn (FormInterface $form) => $form->createView(), $forms),
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    private function common(AdminContext $context, FormConfig $formConfig): array
    {
        return [
            'controllerName' => $context->getEntityName(),
            'entityName' => $context->getEntitySlug(),
            'hasReturnLink' => $formConfig->hasReturnLink,
        ];
    }
}
