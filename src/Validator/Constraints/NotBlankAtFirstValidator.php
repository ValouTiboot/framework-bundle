<?php

namespace Digitix\FrameworkBundle\Validator\Constraints;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotBlankAtFirstValidator extends ConstraintValidator
{
	public $contextProvider;

	public function __construct(ContextProvider $contextProvider)
	{
		$this->contextProvider = $contextProvider->getContext();
	}

	public function validate($value, Constraint $constraint)
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

        if ($this->contextProvider->getEntity()->getInstance()->getId() !== null) {
        	return;
        }

        if (false === $value || (empty($value) && '0' != $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }
}
