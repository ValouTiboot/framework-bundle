<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field;

use Symfony\Component\Validator\Constraint;

/**
 * Turns the "constraints" key of a field definition into validator constraints:
 *
 *   constraints:
 *     - NotBlank                          # short name in Symfony\Component\Validator\Constraints
 *     - { Length: { max: 2000 } }         # single-key map, options are the constructor named arguments
 *     - App\Validator\Siret               # or a fully qualified class name
 */
final class ConstraintFactory
{
    private const NAMESPACE = 'Symfony\\Component\\Validator\\Constraints\\';

    /**
     * @return list<Constraint>
     */
    public static function fromDefinitions(string $fieldName, mixed $definitions): array
    {
        if (null === $definitions) {
            return [];
        }

        if (!\is_array($definitions)) {
            throw new \InvalidArgumentException(sprintf('Field "%s": "constraints" must be a list, %s given.', $fieldName, get_debug_type($definitions)));
        }

        $constraints = [];

        foreach ($definitions as $definition) {
            if (\is_string($definition)) {
                $constraints[] = self::create($fieldName, $definition, []);
                continue;
            }

            if (\is_array($definition) && 1 === \count($definition) && \is_string($name = array_key_first($definition))) {
                $options = $definition[$name] ?? [];

                if (!\is_array($options)) {
                    throw new \InvalidArgumentException(sprintf('Field "%s": the options of constraint "%s" must be a map, %s given.', $fieldName, $name, get_debug_type($options)));
                }

                $constraints[] = self::create($fieldName, $name, $options);
                continue;
            }

            throw new \InvalidArgumentException(sprintf('Field "%s": each constraint is a name ("NotBlank") or a single-key map ({Length: {max: 80}}).', $fieldName));
        }

        return $constraints;
    }

    /**
     * Keeps the constraints of $implicit whose class is not already in $explicit,
     * so that a constraint declared in the YAML replaces its implicit counterpart
     * instead of doubling its error message.
     *
     * @param list<Constraint> $implicit
     * @param list<Constraint> $explicit
     *
     * @return list<Constraint>
     */
    public static function merge(array $implicit, array $explicit): array
    {
        $declared = array_map(static fn (Constraint $constraint): string => $constraint::class, $explicit);

        return array_merge(
            array_values(array_filter($implicit, static fn (Constraint $constraint): bool => !\in_array($constraint::class, $declared, true))),
            $explicit,
        );
    }

    /**
     * @param array<string, mixed> $options named arguments of the constraint constructor
     */
    private static function create(string $fieldName, string $name, array $options): Constraint
    {
        $class = class_exists($name) ? $name : self::NAMESPACE.$name;

        if (!class_exists($class) || !is_subclass_of($class, Constraint::class)) {
            throw new \InvalidArgumentException(sprintf('Field "%s": unknown constraint "%s".', $fieldName, $name));
        }

        foreach (array_keys($options) as $key) {
            if (!\is_string($key)) {
                throw new \InvalidArgumentException(sprintf('Field "%s": the options of constraint "%s" must be named (e.g. {max: 80}).', $fieldName, $name));
            }
        }

        return new $class(...$options);
    }
}
