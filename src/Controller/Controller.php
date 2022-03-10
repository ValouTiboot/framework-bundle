<?php

namespace Digitix\FrameworkBundle\Controller;

use Digitix\FrameworkBundle\Context\Context;
use Digitix\FrameworkBundle\Orm\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Digitix\FrameworkBundle\Factory\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
	protected $context;

    public static function getSubscribedServices(): array
    {
        return [
            'dgtx.form.factory' => '?'. FormFactory::class,
            'dgtx.entity.manager' => '?'. EntityManager::class,
        ] + parent::getSubscribedServices();
    }

    protected function get(string $id): object
    {
        return $this->container->get($id);
    }

    public function setContext(Context $context): object
    {
        $this->context = $context;
        return $this;
    }

    protected function getContext()
    {
        return $this->context;
    }

    protected function display($response): Response
    {
        return new Response($response);
    }

    protected function displayAjax($response): JsonResponse
    {
        return new JsonResponse($response);
    }

    protected function persistEntity()
    {
        return $this->get('dgtx.entity.manager')->persistEntity($this->getContext()->getEntity()->getInstance());
    }

    protected function persistEntities($objects)
    {
        return $this->get('dgtx.entity.manager')->persistEntities($objects);
    }
}
