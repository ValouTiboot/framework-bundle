<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * "Not blank" enforced only while the validated object is not persisted yet
 * (its id is null): typically a password that is mandatory on creation and
 * optional on edition.
 */
final class NotBlankAtFirstValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotBlankAtFirst) {
            throw new UnexpectedTypeException($constraint, NotBlankAtFirst::class);
        }

        if ($constraint->allowNull && null === $value) {
            return;
        }

        if (\is_string($value) && null !== $constraint->normalizer) {
            $value = ($constraint->normalizer)($value);
        }

        $object = $this->context->getObject();
        if (\is_object($object) && method_exists($object, 'getId') && null !== $object->getId()) {
            return;
        }

        if (false === $value || (empty($value) && '0' != $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }
}
