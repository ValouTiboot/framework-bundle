<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Handles the options every field understands (label, required, help,
 * disabled, data, attr, class, constraints); subclasses add their own in
 * getTypeOptions().
 *
 * Validation: the "constraints" key of the YAML is always applied. Front
 * forms (see FieldTypeContext::isFrontForm()) also get implicit constraints,
 * NotBlank for required fields plus what each type adds in
 * getImplicitConstraints(), because the browser "required" attribute alone
 * protects nothing. Admin forms rely on the entity's own constraints.
 */
abstract class AbstractFieldType implements FieldTypeInterface
{
    private const COMMON_OPTIONS = ['required', 'disabled', 'help', 'data', 'mapped', 'empty_data'];

    public function getFormOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = ['translation_domain' => $context->translationDomain];

        if (null !== $field->label) {
            $options['label'] = $field->label;
        }

        foreach (self::COMMON_OPTIONS as $name) {
            if ($field->has($name)) {
                $options[$name] = $field->get($name);
            }
        }

        $constraints = ConstraintFactory::merge(
            $context->isFrontForm() ? $this->getImplicitConstraints($field, $context) : [],
            ConstraintFactory::fromDefinitions($field->name, $field->get('constraints')),
        );

        if ($constraints) {
            $options['constraints'] = $constraints;
        }

        $attr = $this->buildAttributes($field);
        if ($attr) {
            $options['attr'] = $attr;
        }

        $typeOptions = $this->getTypeOptions($field, $context);

        if (isset($typeOptions['attr'])) {
            $typeOptions['attr'] = array_merge($attr, $typeOptions['attr']);
        }

        return array_replace($options, $typeOptions);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        return [];
    }

    /**
     * Constraints a front form gets without declaring them: NotBlank unless
     * the field is "required: false". Types add theirs (Length, Email...) or
     * return [] when the value cannot be blank-checked (switches, collections).
     *
     * @return list<Constraint>
     */
    protected function getImplicitConstraints(FieldConfig $field, FieldTypeContext $context): array
    {
        return false !== $field->isRequired() ? [new NotBlank()] : [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildAttributes(FieldConfig $field): array
    {
        $attr = (array) $field->get('attr', []);

        if ($field->has('class')) {
            $attr['class'] = trim(($attr['class'] ?? '').' '.$field->get('class'));
        }

        return $attr;
    }

    /** Optional fields get an empty choice so that "nothing" can be selected. */
    protected function needsPlaceholder(FieldConfig $field): bool
    {
        return true !== $field->isRequired();
    }

    /**
     * Resolves the "choice" map or the "callback" static callable of a field.
     *
     * @return array<string, mixed>
     */
    protected function resolveChoices(FieldConfig $field): array
    {
        if ($field->has('callback')) {
            $callback = $field->get('callback');

            if (!\is_callable($callback)) {
                throw new \InvalidArgumentException(sprintf('Field "%s": callback "%s" is not callable.', $field->name, \is_string($callback) ? $callback : get_debug_type($callback)));
            }

            return (array) $callback();
        }

        $choices = $field->get('choice', []);

        if (!\is_array($choices)) {
            throw new \InvalidArgumentException(sprintf('Field "%s": "choice" must be a map of label => value, %s given.', $field->name, get_debug_type($choices)));
        }

        return $choices;
    }
}
