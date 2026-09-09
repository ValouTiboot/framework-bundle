<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Entity\Cms;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public CMS page. The "entityName" default lets FrontController load the
 * Cms record from "entityId".
 */
class CmsController extends FrontController
{
    #[Route('/{entityId}-{rewrite}.html', name: 'front_cms_show', defaults: ['entityName' => 'cms'], requirements: ['entityId' => '\d+'], methods: ['GET'])]
    public function show(int $entityId, string $rewrite): Response
    {
        $cms = $this->getCurrentEntity();

        if (!$cms instanceof Cms) {
            throw $this->createNotFoundException(sprintf('No CMS page with id %d.', $entityId));
        }

        if ($cms->getRewrite() !== $rewrite) {
            return $this->redirectToRoute('front_cms_show', ['entityId' => $entityId, 'rewrite' => $cms->getRewrite()], 301);
        }

        $this->breadcrumb[] = [
            'name' => (string) $cms->getName(),
            'url' => $this->generateUrl('front_cms_show', ['entityId' => $entityId, 'rewrite' => $cms->getRewrite()]),
        ];

        // the project template when it exists, otherwise the bundle default
        $template = $this->container->get('twig')->getLoader()->exists('cms/show.html.twig')
            ? 'cms/show.html.twig'
            : '@DigitixFramework/front/cms/show.html.twig';

        return $this->render($template, ['cms' => $cms]);
    }
}
