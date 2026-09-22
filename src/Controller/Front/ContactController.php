<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Utils\Mailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contact form generated from "front_entities.Contact", sent by e-mail to
 * the configured "mailFrom" address.
 */
class ContactController extends FrontController
{
    #[Route('/contact.html', name: 'front_contact', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $this->breadcrumb[] = [
            'name' => $this->trans('Contact', [], 'Front.Breadcrumb'),
            'url' => $this->generateUrl('front_contact'),
        ];

        $form = $this->createFrontForm('Contact');

        if ($form->isSubmitted() && $form->isValid()) {
            $sent = $this->container->get(Mailer::class)->sendMail(
                'contact',
                $this->trans('New contact', [], 'Email.Subject'),
                (array) $form->getData(),
                (string) $this->getConfiguration('mailFrom'),
            );

            if ($sent) {
                $this->addFlash('success', $this->trans('Message succesfully sent.', [], 'Front.Contact.Form'));

                return $this->redirectToRoute('front_contact');
            }

            $this->addFlash('danger', $this->trans('Message could not be sent.', [], 'Front.Contact.Form'));
        }

        return $this->render('contact/index.html.twig', ['form' => $form->createView()]);
    }
}
