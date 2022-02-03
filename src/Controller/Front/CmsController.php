<?php

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Digitix\FrameworkBundle\Controller\Front\FrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/")
 */
class CmsController extends FrontController
{
	/**
     * @Route("{entityId}-{rewrite}.html",
     *	name="front_cms_show",
     *	defaults={"entityName": "cms"},
     *	requirements={"entityId"="\d+"},
     *	methods={"GET"})
     *
     * @return Response
     */
	public function show(string $entityName, int $entityId, string $rewrite)
	{
		$cms = $this->getContext()->getEntity()->getInstance();

		if ($entityId != 1 && $entityId != 3)
			$this->breadcrumb[] = [
	            'name' => $cms->getName(),
	            'url' => $this->generateUrl('front_cms_show', ['entityId' => $entityId, 'rewrite' => $cms->getRewrite()], 0),
	        ];

		if ($cms->getRewrite() != $rewrite)
			return $this->redirectToRoute('front_cms_show', ['entityId' => $entityId, 'rewrite' => $cms->getRewrite()], 301);

		return $this->render('@ApporteurImmo/cms/show.twig', ['cms' => $cms]);
	}
}
