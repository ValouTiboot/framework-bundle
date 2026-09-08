<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Provider;

use Digitix\FrameworkBundle\Entity\Language;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Access to the site languages, memoised for the duration of the request
 * (the container resets it between requests in long-running runtimes).
 */
final class LanguageProvider implements ResetInterface
{
    private ?Language $default = null;
    /** @var Language[]|null */
    private ?array $active = null;

    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function getDefaultLanguage(): Language
    {
        return $this->default ??= $this->registry->getRepository(Language::class)->findOneBy(['defaultLanguage' => true])
            ?? throw new \RuntimeException('No default language is defined. Create a Language row with defaultLanguage = true (or load the bundle fixtures).');
    }

    public function getDefaultLanguageId(): int
    {
        return (int) $this->getDefaultLanguage()->getId();
    }

    /**
     * Active languages, default language first.
     *
     * @return Language[]
     */
    public function getActiveLanguages(): array
    {
        return $this->active ??= $this->registry->getRepository(Language::class)->findBy(
            ['active' => true],
            ['defaultLanguage' => 'DESC', 'id' => 'ASC']
        );
    }

    public function reset(): void
    {
        $this->default = null;
        $this->active = null;
    }
}
