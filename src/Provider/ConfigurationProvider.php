<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Provider;

use Digitix\FrameworkBundle\Entity\Configuration;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Access to the global "Configuration" rows (mailFrom, gtm, ssl, ...),
 * memoised for the duration of the request (the container resets it between
 * requests in long-running runtimes).
 */
final class ConfigurationProvider implements ResetInterface
{
    /** @var array<string, Configuration>|null */
    private ?array $rows = null;

    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function get(string $name): ?Configuration
    {
        return $this->rows()[$name] ?? null;
    }

    public function getValue(string $name, ?string $default = null): ?string
    {
        return $this->get($name)?->getValue() ?? $default;
    }

    /** @return array<string, string|null> name => value */
    public function all(): array
    {
        return array_map(static fn (Configuration $row) => $row->getValue(), $this->rows());
    }

    /** Forget the memoised rows (call after saving Configuration entities). */
    public function reset(): void
    {
        $this->rows = null;
    }

    /** @return array<string, Configuration> */
    private function rows(): array
    {
        if (null === $this->rows) {
            $this->rows = [];
            foreach ($this->registry->getRepository(Configuration::class)->findAll() as $row) {
                $this->rows[$row->getName()] = $row;
            }
        }

        return $this->rows;
    }
}
