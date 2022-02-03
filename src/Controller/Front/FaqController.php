<?php

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Digitix\FrameworkBundle\Controller\Front\FrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class FaqController extends FrontController
{
	/**
     * @Route("faq.html",
     *	name="front_faq_show",
     *	defaults={"entityName": "faq"},
     *	methods={"GET"})
     *
     * @return Response
     */
	public function show(string $entityName): Response
	{
		$this->breadcrumb[] = [
            'name' => $this->getContext()->trans('FAQ', [], 'Front.Breadcrumb'),
            'url' => $this->generateUrl('front_faq_show', [], 0),
        ];

	    $faqs = $this->get('dgtx.entity.repository')->findBy(['active' => 1], ['position' => 'asc']);

		return $this->render('@ApporteurImmo/faq/show.twig', ['faqs' => $faqs]);
	}
}
