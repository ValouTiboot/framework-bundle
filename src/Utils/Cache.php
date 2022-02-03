<?php

namespace Digitix\FrameworkBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class Cache
{
	private $kernel;

	public function __construct(KernelInterface $kernel)
	{
		$this->kernel = $kernel;
	}

	public function cacheClear()
    {
        $env = $this->kernel->getEnvironment();

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'cache:clear',
            '--env' => $env
        ));

        return $application->run($input);
    }
}
