<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Form;

use Digitix\FrameworkBundle\Admin\Config\FormConfig;
use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeRegistry;
use Digitix\FrameworkBundle\Admin\Field\Type\SubmitFieldType;
use Digitix\FrameworkBundle\Admin\Filter\FilterTypeRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds Symfony forms from a FormConfig (entity forms) or from the list
 * filters of an entity.
 */
final class AdminFormBuilder
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly FieldTypeRegistry $fieldTypes,
        private readonly FilterTypeRegistry $filterTypes,
    ) {
    }

    /**
     * Form for the managed entity of the context (or for $data when given),
     * already bound to the request.
     *
     * @param array<string, mixed> $options Symfony form options overriding the defaults
     */
    public function createForm(AdminContext $context, array $options = [], mixed $data = null, ?FormConfig $formConfig = null): FormInterface
    {
        $formConfig ??= $context->getEntityConfig()->form;
        $data ??= $context->getEntity();

        $typeContext = new FieldTypeContext($formConfig->translationDomain, $context, \is_object($data) ? $data : null);

        return $this->createFromDefinitions(
            strtolower($context->getEntityName()),
            $this->buildDefinitions($formConfig, $typeContext),
            $data,
            $options,
            $context->getRequest(),
        );
    }

    /**
     * Filters form of the list, bound to the query string.
     */
    public function createFiltersForm(AdminContext $context): FormInterface
    {
        $definitions = [];
        foreach ($context->getEntityConfig()->list->filters as $filter) {
            $type = $this->filterTypes->get($filter->type);
            $definitions[] = [
                'name' => $filter->name,
                'type' => $type->getFormType($filter),
                'options' => $type->getFormOptions($filter),
            ];
        }

        $form = $this->formFactory->createNamed('filters', AdminFiltersType::class, null, ['fields' => $definitions]);
        $form->handleRequest($context->getRequest());

        return $form;
    }

    /**
     * Child definitions for a FormConfig, plus the automatic submit button.
     *
     * @return list<array{name: string, type: string, options: array<string, mixed>}>
     */
    public function buildDefinitions(FormConfig $formConfig, FieldTypeContext $typeContext): array
    {
        $definitions = [];
        $hasSubmit = false;

        foreach ($formConfig->fields as $field) {
            $type = $this->fieldTypes->get($field->type);
            $hasSubmit = $hasSubmit || 'submit' === $field->type || 'save' === $field->name;

            $definitions[] = [
                'name' => $field->name,
                'type' => $type->getFormType($field, $typeContext),
                'options' => $type->getFormOptions($field, $typeContext),
            ];
        }

        if ($formConfig->hasAutoSubmitButton && !$hasSubmit) {
            $definitions[] = SubmitFieldType::defaultDefinition();
        }

        return $definitions;
    }

    /**
     * @param list<array{name: string, type: string, options?: array<string, mixed>}> $definitions
     * @param array<string, mixed>                                                    $options
     */
    public function createFromDefinitions(string $name, array $definitions, mixed $data, array $options = [], ?Request $request = null): FormInterface
    {
        $form = $this->formFactory->createNamed(
            $name,
            AdminFormType::class,
            $data,
            array_replace(['method' => 'POST', 'fields' => $definitions], $options),
        );

        if (null !== $request) {
            $form->handleRequest($request);
        }

        return $form;
    }
}
