<?php

namespace Digitix\FrameworkBundle\Controller;

use Digitix\FrameworkBundle\Context\Context;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller extends AbstractController
{
	protected $context;

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
        return $this->get('dgtx.entity.persister')->persistObject($this->getContext()->getEntity()->getInstance());
    }

    protected function persistEntities($objects)
    {
        return $this->get('dgtx.entity.persister')->persistObjects($objects);
    }
}
