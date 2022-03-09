<?php

namespace Digitix\FrameworkBundle\EventListener;

use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Digitix\FrameworkBundle\Config\AdminMenuConfigInterface;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;

class AdminControllerListener
{
    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var \Symfony\Component\Security\Core\Security
     */
    private $security;

    private $adminMenu;

    public function __construct(Environment $twig, Security $security, AdminMenuConfigInterface $adminMenu)
    {
        $this->twig     = $twig;
        $this->security = $security;
        $this->adminMenu = $adminMenu;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $controller = null;
        $request = $event->getRequest();
        $route_params = $request->attributes->get('_route_params');

        if (!isset($route_params['entityName'])) {
            return;
        }

        $xplodeController = explode('::', $request->attributes->get('_controller'));
        $controllerFqcn = preg_replace('@(Admin.Admin)(.*)(Controller)@', '$1'.ucfirst($route_params['entityName']).'$3', $xplodeController[0]);
        // $appControllerFqcn = 'App\\Controller\\Admin\\Admin'.ucfirst($route_params['entityName']).'Controller';
        $adminDigitixControllerFqcn = 'Digitix\\FrameworkBundle\\Controller\\Admin\\Admin'.ucfirst($route_params['entityName']).'Controller';

        // dump(class_exists($appControllerFqcn));
        // dump(class_exists($digitixControllerFqcn));

        if (class_exists($controllerFqcn)) {
            $controller = $controllerFqcn;
        // } elseif (class_exists($appControllerFqcn)) {
        //     $controller = $appControllerFqcn;
        } elseif (class_exists($adminDigitixControllerFqcn)) {
            $controller = 'dgtx.admin.controller.'.$route_params['entityName'];
        }

        dump($controller);

        if ($controller !== null) {
            $request->attributes->set('_controller', $controller.'::'.$xplodeController[1]);
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $currentControllerInstance = $this->getCurrentControllerInstance($event);

        if ($this->isAdminRequest($currentControllerInstance) === true) {
            // $loader = $this->twig->getLoader();
            // $loader->setPaths('templates/admin');
            // $this->twig->setLoader($loader);
        }

        $user = $this->security->getUser();
dump($event);
        // $this->twig->addGlobal( 'dgtx', $results[0]->getName() );
        $this->twig->addGlobal('user', $user);
        $this->twig->addGlobal('adminMenu', $this->adminMenu->getAdminMenuConfig());
    }

    private function isAdminRequest($currentControllerInstance) : bool
    {
        return $currentControllerInstance instanceof AdminController;
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

