<?php

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Controller\Front\FrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends FrontController
{
	/**
     * @Route("/", name="dgtx_index", methods={"GET"})
     *
     * @return Response
     */
	public function index()
	{
		return $this->render('index.twig', []);
	}
}
