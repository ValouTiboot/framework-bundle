<?php

namespace Digitix\FrameworkBundle\ArgumentResolver;

use Digitix\FrameworkBundle\Context\Context;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ContextResolver implements ArgumentValueResolverInterface
{
    private $contextProvider;

    public function __construct(ContextProvider $contextProvider)
    {
        $this->contextProvider = $contextProvider;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Context::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield $this->contextProvider->getContext();
    }
}
