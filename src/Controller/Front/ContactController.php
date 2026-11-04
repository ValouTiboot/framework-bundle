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
            if ($this->isSpam($form)) {
                // Same answer as for a genuine message, so that bots learn nothing; nothing is sent.
                $this->addFlash('success', $this->trans('Message succesfully sent.', [], 'Front.Contact.Form'));

                return $this->redirectToRoute('front_contact');
            }

            $data = (array) $form->getData();
            // Reply-To the visitor (fields "mail" and "lastname" of the form), so a plain reply reaches them.
            // The address is checked again here: a project form without the "mail" constraint would make the Mailer throw.
            $replyTo = (string) ($data['mail'] ?? '');
            $replyTo = false !== filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? $replyTo : '';
            $replyToName = (string) ($data['lastname'] ?? '');

            $sent = $this->container->get(Mailer::class)->sendMail(
                'contact',
                $this->trans('New contact', [], 'Email.Subject'),
                $data,
                (string) $this->getConfiguration('mailFrom'),
                replyTo: '' !== $replyTo ? $replyTo : null,
                replyToName: '' !== $replyToName ? $replyToName : null,
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
