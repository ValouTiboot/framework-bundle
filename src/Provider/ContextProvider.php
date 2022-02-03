<?php

namespace Digitix\FrameworkBundle\Provider;

use Digitix\FrameworkBundle\Context\Context;
use Symfony\Component\HttpFoundation\RequestStack;

final class ContextProvider
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getContext(): ?Context
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return null !== $currentRequest ? $currentRequest->get(Context::CONTEXT_ID) : null;
    }
}
