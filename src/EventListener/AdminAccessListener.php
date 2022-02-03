<?php

namespace Digitix\FrameworkBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class AdminAccessListener
{
	/**
	 * Description
	 * @param ControllerEvent $event
	 * @return type
	 */
	public function onKernelController(ControllerEvent $event): void
    {
        $currentControllerInstance = $this->getCurrentControllerInstance($event);

        if ($this->isAdminRequest($currentControllerInstance) === false)
        	return;

        dump('do check on view|read|create|edit|delete');
        $currentControllerInstance->checkAccess('read', null, 'Access Not permitted.');
    }

    private function isAdminRequest($currentControllerInstance) : bool
    {
    	return $currentControllerInstance instanceof \Digitix\FrameworkBundle\Controller\AdminController;
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
