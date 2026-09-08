<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @see NotBlankAtFirstValidator
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class NotBlankAtFirst extends Constraint
{
    public string $message = 'The value should not be blank.';
    public bool $allowNull = false;
    /** @var callable|null */
    public $normalizer;

    /**
     * @param array<string, mixed>|null $options
     * @param string[]|null             $groups
     */
    public function __construct(?array $options = null, ?string $message = null, ?bool $allowNull = null, ?callable $normalizer = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->allowNull = $allowNull ?? $this->allowNull;
        $this->normalizer = $normalizer ?? $this->normalizer;

        if (null !== $this->normalizer && !\is_callable($this->normalizer)) {
            throw new InvalidArgumentException(sprintf('The "normalizer" option must be a valid callable ("%s" given).', get_debug_type($this->normalizer)));
        }
    }
}
