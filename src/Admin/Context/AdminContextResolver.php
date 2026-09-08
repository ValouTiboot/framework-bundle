<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Context;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Injects the AdminContext into any controller action typed with it.
 */
final class AdminContextResolver implements ValueResolverInterface
{
    /** @return iterable<int, AdminContext|null> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (AdminContext::class !== $argument->getType()) {
            return [];
        }

        $context = $request->attributes->get(AdminContext::ATTRIBUTE);

        if (!$context instanceof AdminContext) {
            if ($argument->isNullable()) {
                return [null];
            }

            throw new \LogicException(sprintf(
                'No AdminContext is available for route "%s". AdminContext can only be injected in admin routes declaring an "entityName" parameter.',
                $request->attributes->get('_route', '?')
            ));
        }

        return [$context];
    }
}
