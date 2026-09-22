<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Runs "cache:clear" from the admin (Performance page).
 */
final class Cache
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    /** @return int console exit code, 0 on success */
    public function cacheClear(): int
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        return $application->run(new ArrayInput([
            'command' => 'cache:clear',
            '--env' => $this->kernel->getEnvironment(),
        ]), new NullOutput());
    }
}
