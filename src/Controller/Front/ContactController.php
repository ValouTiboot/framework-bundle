<?php

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Controller\Front\FrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/contact")
 */
class ContactController extends FrontController
{
	/**
     * @Route(".html", name="front_contact", methods={"GET","POST"})
     *
     * @return Response
     */
	public function index()
	{
        $this->breadcrumb[] = [
            'name' => $this->getContext()->trans('Contact', [], 'Front.Breadcrumb'),
            'url' => $this->generateUrl('front_contact', [], 0),
        ];

		$fields = $this->get('dgtx.field.factory')->build($this->get('dgtx.front.form.config')->setEntityName('Contact'));
		$form = $this->get('dgtx.form.factory')->buildForm($fields);

		if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
        	$mailer = $this->get('dgtx.mailer');

        	if ($mailer->sendMail('contact',
                $this->getContext()->trans('New contact', [], 'Email.Subject'),
                $data,
                $this->getContext()->getConfiguration('mailFrom')
            ))
            {
	            $this->addFlash('success', $this->getContext()->trans('Message succesfully sent.', [], 'Front.Contact.Form'));
	            return $this->redirectToRoute('front_contact');
            }

        }

		return $this->render('@ApporteurImmo/contact/index.twig', ['form' => $form->createView()]);
	}
}