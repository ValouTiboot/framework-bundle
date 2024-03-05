<?php

namespace Digitix\FrameworkBundle\EventListener;

use Digitix\FrameworkBundle\Context\Context;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Configuration;
use Digitix\FrameworkBundle\Factory\ContextFactory;
use Digitix\FrameworkBundle\Factory\EntityFactory;
use Digitix\FrameworkBundle\Provider\EntityRepositoryProvider;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Doctrine\Common\Collections\ArrayCollection;

class ContextListener
{
	private $contextFactory;
    private $entityFactory;
    private $entityRepository;

	public function __construct(
        ContextFactory $contextFactory,
        EntityFactory $entityFactory,
        EntityRepositoryProvider $entityRepository
    ) {
		$this->contextFactory = $contextFactory;
        $this->entityFactory = $entityFactory;
        $this->entityRepository = $entityRepository;
	}
	/**
	 * Description
	 * @param ControllerEvent $event
	 * @return type
	 */
	public function onKernelController(ControllerEvent $event) : void
    {
        $controllerInstance = $this->getCurrentControllerInstance($event);
        $routeParams = $event->getRequest()->attributes->get('_route_params');

        $entityName = isset($routeParams['entityName']) ? $routeParams['entityName'] : null;
        $entityId = isset($routeParams['entityId']) ? $routeParams['entityId'] : null;

        $languageRepository = $this->entityRepository->getRepository(Language::class);
        $language = $languageRepository->findOneBy(['defaultLanguage' => 1]);
        $event->getRequest()->setLocale($language->getLocale());

        $configRepository = $this->entityRepository->getRepository(Configuration::class);
        $congigurations = $configRepository->findAll();
        $_globals = new ArrayCollection();

        foreach ($congigurations as $congiguration) {
            $_globals->set($congiguration->getName(), $congiguration->getValue());
        }

        // creating the context. if the current request already has an AdminContext object, do nothing
        if (null === $context = $this->getContext($event)) {
        	$context = $this->contextFactory->createContext();
            $context
                ->setEntityName($entityName)
                ->setRequest($event->getRequest())
                ->setController($controllerInstance)
                ->setLanguage($language)
                ->setConfiguration($_globals)
            ;

            if (is_object($controllerInstance) && method_exists($controllerInstance, 'setContext')) {
                $controllerInstance->setContext($context);
            }

        	$this->setContext($event, $context);

            if ($entityName !== null) {
                $entity = $this->entityFactory->build($entityName, $entityId);
                $context->setEntity($entity);
            }

            $context->getTwig()->addGlobal('dgtxConfiguration', $context->getConfigurations());
        }
    }

    private function setContext(ControllerEvent $event, Context $context) : void
    {
    	$event->getRequest()->attributes->set(Context::CONTEXT_ID, $context);
    }

    private function getContext(ControllerEvent $event) : ?Context
    {
    	return $event->getRequest()->attributes->get(Context::CONTEXT_ID);
    }

    private function getCurrentControllerInstance(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (!\is_array($controller))
            return null;

        return $controller[0];
    }
}
