<?php

namespace Digitix\FrameworkBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NotBlankAtFirst extends Constraint
{
    public $message = 'The value should not be blank.';
    public $allowNull = false;
    public $normalizer;
    public $context;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null !== $this->normalizer && !\is_callable($this->normalizer)) {
            throw new InvalidArgumentException(sprintf('The "normalizer" option must be a valid callable ("%s" given).', \is_object($this->normalizer) ? \get_class($this->normalizer) : \gettype($this->normalizer)));
        }
    }
}
