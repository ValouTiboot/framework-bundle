<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Provider\DigitixParameterProvider;

final class DigitixParameterFactory
{
    private $params = [];
    private $context;

    public function __construct($params, ContextProvider $contextProvider)
    {
        $this->params = $params;
        $this->context = $contextProvider->getContext();
    }

    public function build($keyName = null, $valueName = null) 
    {
        if (null === $keyName) {
            $keyName = 'admin_entities';
        }

        if (null === $valueName) {
            $valueName = $this->context->getEntityName();
        }

        return new DigitixParameterProvider($this->params, $keyName, $valueName);
    }
}
