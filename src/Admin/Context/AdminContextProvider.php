<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Context;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gives services access to the current AdminContext at call time.
 *
 * Only ever call getContext() from inside a method that runs during an admin
 * request (a Twig runtime, a form type buildForm...), never from a constructor.
 */
final class AdminContextProvider
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getContext(): ?AdminContext
    {
        $request = $this->requestStack->getMainRequest();

        return $request?->attributes->get(AdminContext::ATTRIBUTE);
    }

    public function hasContext(): bool
    {
        return null !== $this->getContext();
    }
}
