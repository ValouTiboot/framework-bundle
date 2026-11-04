<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Configuration;
use Digitix\FrameworkBundle\Utils\SpamGuard;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Mime\Email;

/**
 * Contact form of the recipe (front_entities.Contact): server-side
 * validation, anti-spam fields, e-mail sent with a Reply-To.
 */
final class ContactFormTest extends AdminTestCase
{
    private const VALID = [
        'contact[lastname]' => 'Doe',
        'contact[firstname]' => 'Jane',
        'contact[mail]' => 'jane@example.test',
        'contact[message]' => 'Hello there',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $row = $this->em()->getRepository(Configuration::class)->findOneBy(['name' => 'mailFrom']);
        self::assertInstanceOf(Configuration::class, $row);
        $row->setValue('contact@digitix.test');
        $this->em()->flush();
    }

    public function testTheFormCarriesTheAntiSpamFields(): void
    {
        $crawler = $this->client->request('GET', '/contact.html');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('.dgtx-honeypot input[name="contact[website]"]'));
        self::assertSame('true', $crawler->filter('.dgtx-honeypot')->attr('aria-hidden'));
        self::assertMatchesRegularExpression('/^\d+\.[0-9a-f]{64}$/', (string) $crawler->filter('input[name="contact[renderedAt]"]')->attr('value'));
    }

    public function testAnEmptySubmissionIsRejectedServerSide(): void
    {
        $crawler = $this->client->request('GET', '/contact.html');
        $crawler = $this->client->submit($crawler->filter('form[name="contact"]')->form());

        self::assertResponseIsSuccessful('no redirect: the page shows the errors');
        self::assertGreaterThanOrEqual(3, $crawler->filter('form[name="contact"] ul li')->count(), 'lastname, mail and message are blank');
        self::assertEmailCount(0);
    }

    public function testAnInvalidEmailIsRejected(): void
    {
        $crawler = $this->client->request('GET', '/contact.html');
        $crawler = $this->client->submit($crawler->filter('form[name="contact"]')->form(), $this->values(['contact[mail]' => 'not-an-email'], -10));

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('form[name="contact"] ul li'));
        self::assertEmailCount(0);
    }

    public function testAGenuineMessageIsSentWithAReplyTo(): void
    {
        $crawler = $this->client->request('GET', '/contact.html');
        $this->client->submit($crawler->filter('form[name="contact"]')->form(), $this->values([], -10));

        self::assertResponseRedirects('/contact.html');
        self::assertEmailCount(1);

        $email = self::getMailerMessage();
        self::assertInstanceOf(Email::class, $email);
        self::assertSame('contact@digitix.test', $email->getTo()[0]->getAddress());
        self::assertSame('jane@example.test', $email->getReplyTo()[0]->getAddress());
        self::assertSame('Doe', $email->getReplyTo()[0]->getName());
        self::assertStringContainsString('Hello there', (string) $email->getTextBody());

        $crawler = $this->client->followRedirect();
        self::assertCount(1, $crawler->filter('.flash-success'));
    }

    public function testASubmissionSentTooFastIsSilentlyDropped(): void
    {
        $crawler = $this->client->request('GET', '/contact.html');
        // the rendered timestamp is a few milliseconds old: a bot's speed
        $this->client->submit($crawler->filter('form[name="contact"]')->form(), self::VALID);

        self::assertResponseRedirects('/contact.html');
        self::assertEmailCount(0);

        $crawler = $this->client->followRedirect();
        self::assertCount(1, $crawler->filter('.flash-success'), 'bots get the same answer as humans');
    }

    public function testAFilledHoneypotIsSilentlyDropped(): void
    {
        $crawler = $this->client->request('GET', '/contact.html');
        $this->client->submit($crawler->filter('form[name="contact"]')->form(), $this->values(['contact[website]' => 'http://spam.test'], -10));

        self::assertResponseRedirects('/contact.html');
        self::assertEmailCount(0);
    }

    /**
     * Valid values with the timestamp field rendered $ageInSeconds ago.
     *
     * @param array<string, string> $overrides
     *
     * @return array<string, string>
     */
    private function values(array $overrides, int $age): array
    {
        $guard = static::getContainer()->get(SpamGuard::class);
        self::assertInstanceOf(SpamGuard::class, $guard);

        return array_merge(self::VALID, ['contact[renderedAt]' => $guard->sign(time() + $age)], $overrides);
    }
}
