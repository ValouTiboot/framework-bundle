<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Utils;

use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Sends a templated e-mail. Templates live under "{templatePath}{locale}/":
 * "<template>.txt" and "<template>.html.twig".
 */
final class Mailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ConfigurationProvider $configuration,
        private readonly LanguageProvider $languages,
    ) {
    }

    /**
     * @param array<string, mixed>       $templateVars
     * @param string|string[]            $to
     * @param string|string[]|null       $cc
     * @param string|string[]|null       $bcc
     */
    public function sendMail(
        string $template,
        string $subject,
        array $templateVars,
        string|array $to,
        ?string $toName = null,
        ?string $from = null,
        ?string $fromName = null,
        ?string $fileAttachment = null,
        string $templatePath = '@Mail/',
        string|array|null $cc = null,
        string|array|null $bcc = null,
        ?string $replyTo = null,
        ?string $replyToName = null,
    ): bool {
        $locale = $this->languages->getDefaultLanguage()->getLocale();

        $email = (new TemplatedEmail())
            ->from($this->address($from ?? $this->configuration->getValue('mailFrom', ''), $fromName ?? $this->configuration->getValue('mailFromName')))
            ->to(...$this->addresses($to, $toName))
            ->subject($subject)
            ->textTemplate(sprintf('%s%s/%s.txt', $templatePath, $locale, $template))
            ->htmlTemplate(sprintf('%s%s/%s.html.twig', $templatePath, $locale, $template))
            ->context(array_merge([
                'projectName' => $this->configuration->getValue('siteName', ''),
                'subject' => $subject,
            ], $templateVars));

        if (null !== $cc) {
            $email->cc(...$this->addresses($cc));
        }

        if (null !== $bcc) {
            $email->bcc(...$this->addresses($bcc));
        }

        if (null !== $replyTo) {
            $email->replyTo($this->address($replyTo, $replyToName));
        }

        if (null !== $fileAttachment) {
            $email->attachFromPath($fileAttachment);
        }

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface) {
            return false;
        }
    }

    private function address(string $email, ?string $name = null): Address
    {
        return new Address($email, (string) $name);
    }

    /**
     * @param string|string[] $emails
     *
     * @return Address[]
     */
    private function addresses(string|array $emails, ?string $name = null): array
    {
        if (\is_string($emails)) {
            return [$this->address($emails, $name)];
        }

        return array_map(fn (string $email) => $this->address($email), $emails);
    }
}
