<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Context;

use Digitix\FrameworkBundle\Admin\Config\EntityConfig;
use Digitix\FrameworkBundle\Admin\Persistence\EntityInstantiator;
use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Symfony\Component\HttpFoundation\Request;

final class AdminContextFactory
{
    public function __construct(
        private readonly LanguageProvider $languages,
        private readonly ConfigurationProvider $configuration,
        private readonly EntityInstantiator $instantiator,
    ) {
    }

    public function create(EntityConfig $entityConfig, Request $request): AdminContext
    {
        $entityId = $request->attributes->get('entityId');

        return new AdminContext(
            $entityConfig,
            $request,
            null === $entityId ? null : (int) $entityId,
            $this->languages->getDefaultLanguage(),
            $this->configuration,
            $this->instantiator,
        );
    }
}
