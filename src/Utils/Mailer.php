<?php

namespace Digitix\FrameworkBundle\Utils;

use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class Mailer
{
	private $mailer;
    private $context;
    private $configurationProvider;

	public function __construct(MailerInterface $mailer, ConfigurationProvider $configurationProvider, ContextProvider $contextProvider)
	{
		$this->mailer = $mailer;
        $this->configurationProvider = $configurationProvider;
		$this->context = $contextProvider->getContext();
	}

	public function sendMail(
		$template,
        $subject,
        $templateVars = [],
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $templatePath = '@Mail/',
        $cc = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null
    ) {

        if ($fromName !== null && $from !== null)
            $from = Address::create($fromName.' <'.$from.'>');
        else if ($from !== null)
            $from = new Address($from);
        else
        {
            $mailFrom = $this->configurationProvider->get('mailFrom');
            $mailFromName = $this->configurationProvider->get('mailFromName');
            $from = Address::create($mailFromName->getValue().' <'.$mailFrom->getValue().'>');
		}

        $tplVars = [
            'projectName' => 'ApporteurImmo',
            'subject' => $subject,
        ];

        $locale = $this->context->getLanguage()->getLocale();
        $templateFullPath = '../templates/mails/'.$locale.'/';

        if (!file_exists($templateFullPath.$template.'.txt'))
            throw new FileNotFoundException('Unable to locate file, '.$templateFullPath.$template.'.txt is missing.');

        if (!file_exists($templateFullPath.$template.'.html.twig'))
            throw new FileNotFoundException('Unable to locate file, '.$templateFullPath.$template.'.html.twig is missing.');;

		$email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->textTemplate($templatePath.$locale.'/'.$template.'.txt')
            ->htmlTemplate($templatePath.$locale.'/'.$template.'.html.twig')
            ->context(array_merge($tplVars, $templateVars))
        ;

        if ($cc !== null)
            $email->cc($cc);
        if ($bcc !== null)
            $email->bcc($bcc);
        if ($replyTo !== null)
            $email->replyTo($replyTo);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            dump($e->getMessage());
            return false;
        }
	}
}
