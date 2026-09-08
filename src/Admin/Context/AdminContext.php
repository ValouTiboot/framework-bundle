<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Context;

use Digitix\FrameworkBundle\Admin\Config\EntityConfig;
use Digitix\FrameworkBundle\Admin\Persistence\EntityInstantiator;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Per-request state of the admin: which entity is being managed, on which
 * record, in which language.
 *
 * Created once per admin request by AdminRequestListener, stored in the
 * request attributes, and injected into controller actions through
 * AdminContextResolver. Services must never read it from a constructor.
 */
final class AdminContext
{
    public const ATTRIBUTE = '_dgtx_admin_context';

    private ?object $entity = null;
    private bool $entityResolved = false;

    public function __construct(
        private readonly EntityConfig $entityConfig,
        private readonly Request $request,
        private readonly ?int $entityId,
        private readonly Language $language,
        private readonly ConfigurationProvider $configuration,
        private readonly EntityInstantiator $instantiator,
    ) {
    }

    public function getEntityConfig(): EntityConfig
    {
        return $this->entityConfig;
    }

    /** Canonical entity name ("CmsCategory"). */
    public function getEntityName(): string
    {
        return $this->entityConfig->name;
    }

    /** URL segment ("cmsCategory"). */
    public function getEntitySlug(): string
    {
        return $this->entityConfig->getSlug();
    }

    public function getEntityClass(): ?string
    {
        return $this->entityConfig->class;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    /**
     * The managed record: loaded by id, or a fresh instance (with its
     * translations initialised) when creating. Null for virtual entities.
     *
     * Resolved lazily so that anonymous requests never hit the database
     * before the firewall kicks in.
     */
    public function getEntity(): ?object
    {
        if (!$this->entityResolved) {
            $this->entity = $this->instantiator->instantiate($this->entityConfig, $this->entityId);
            $this->entityResolved = true;
        }

        return $this->entity;
    }

    public function hasEntity(): bool
    {
        return null !== $this->getEntity();
    }

    /** Lets a controller substitute its own record (e.g. one it just created). */
    public function setEntity(?object $entity): void
    {
        $this->entity = $entity;
        $this->entityResolved = true;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    /** Value of a global "Configuration" row (mailFrom, gtm, ...). */
    public function getConfiguration(string $key): ?string
    {
        return $this->configuration->getValue($key);
    }

    /** @return array<string, string|null> */
    public function getConfigurations(): array
    {
        return $this->configuration->all();
    }
}
