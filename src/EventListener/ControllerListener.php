<?php

namespace Digitix\FrameworkBundle\EventListener;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class ControllerListener
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    private $context;

    public function __construct(Environment $twig, ContextProvider $contextProvider)
    {
        $this->twig     = $twig;
        $this->context  = $contextProvider->getContext();
    }

    public function onKernelController(ControllerEvent $event): void
    {
    	$currentControllerInstance = $this->getCurrentControllerInstance($event);

    	if ($this->isFrontRequest($currentControllerInstance))
    	{
            $this->twig->addGlobal('dgtxMeta', $currentControllerInstance->assignMetaVars());
    	}
    }

    private function isFrontRequest($currentControllerInstance) : bool
    {
        return $currentControllerInstance instanceof \Digitix\FrameworkBundle\Controller\Front\FrontController;
    }

    private function isMainRequest($currentControllerInstance) : bool
    {
        return $currentControllerInstance instanceof \Digitix\FrameworkBundle\Controller\Controller;
    }

    private function getCurrentControllerInstance(ControllerEvent $event)
    {
        $controller = $event->getController();

        // if the controller is defined in a class, $controller is an array
        // otherwise do nothing because it's a Closure (rare but possible in Symfony)
        if (!\is_array($controller)) {
            return null;
        }

        return $controller[0];
    }
}
