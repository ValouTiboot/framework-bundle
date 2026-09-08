<?php

declare(strict_types=1);

use Digitix\FrameworkBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    Configuration::buildTree($definition->rootNode());
};
