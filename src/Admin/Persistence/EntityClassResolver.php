<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Persistence;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;

/**
 * Runtime resolution of an entity name ("cmsCategory", "Language"...) to its
 * Doctrine class: configured admin entities first, then the configured
 * namespaces by convention.
 */
final class EntityClassResolver
{
    /**
     * @param string[] $namespaces
     */
    public function __construct(
        private readonly AdminConfig $config,
        private readonly array $namespaces,
    ) {
    }

    public function resolve(string $name): string
    {
        return $this->tryResolve($name)
            ?? throw new \InvalidArgumentException(sprintf('No entity class found for "%s" (searched in %s).', $name, implode(', ', $this->namespaces)));
    }

    public function tryResolve(string $name): ?string
    {
        if (class_exists($name)) {
            return $name;
        }

        if ($this->config->hasEntity($name) && null !== ($class = $this->config->getEntity($name)->class)) {
            return $class;
        }

        foreach ($this->namespaces as $namespace) {
            $candidate = rtrim($namespace, '\\').'\\'.ucfirst($name);

            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
