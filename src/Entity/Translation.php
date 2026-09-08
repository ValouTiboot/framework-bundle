<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Digitix\FrameworkBundle\Translation\TranslationStatus;
use Doctrine\ORM\Mapping as ORM;

/**
 * One translation of one message key, in one domain, for one locale.
 *
 * Keys are the English texts (or dotted identifiers) written in the code;
 * they can be long and contain HTML, hence the TEXT column and the md5 used
 * by the unique index.
 */
#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\Table(name: 'translation')]
#[ORM\UniqueConstraint(name: 'uniq_translation_entry', columns: ['domain', 'locale', 'key_hash'])]
#[ORM\Index(name: 'idx_translation_locale_status', columns: ['locale', 'status'])]
class Translation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 120)]
    private ?string $domain = null;

    #[ORM\Column(name: 'message_key', type: 'text')]
    private ?string $key = null;

    #[ORM\Column(name: 'key_hash', type: 'string', length: 32)]
    private ?string $keyHash = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $locale = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: 'string', length: 12, enumType: TranslationStatus::class)]
    private TranslationStatus $status = TranslationStatus::Missing;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateUpd = null;

    public function __construct()
    {
        $this->dateUpd = new \DateTime();
    }

    public static function create(string $domain, string $key, string $locale): self
    {
        return (new self())->setDomain($domain)->setKey($key)->setLocale($locale);
    }

    public static function hashKey(string $key): string
    {
        return md5($key);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        $this->keyHash = self::hashKey($key);

        return $this;
    }

    public function getKeyHash(): ?string
    {
        return $this->keyHash;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /** An empty value means "not translated". */
    public function setValue(?string $value): self
    {
        $value = null === $value ? null : trim($value);
        $this->value = '' === $value ? null : $value;
        $this->dateUpd = new \DateTime();

        if (TranslationStatus::Obsolete !== $this->status) {
            $this->status = null === $this->value ? TranslationStatus::Missing : TranslationStatus::Translated;
        }

        return $this;
    }

    public function isTranslated(): bool
    {
        return null !== $this->value;
    }

    public function getStatus(): TranslationStatus
    {
        return $this->status;
    }

    /** Status as a string, for list columns. */
    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function isObsolete(): bool
    {
        return TranslationStatus::Obsolete === $this->status;
    }

    /** The key disappeared from the code. */
    public function markObsolete(): self
    {
        $this->status = TranslationStatus::Obsolete;
        $this->dateUpd = new \DateTime();

        return $this;
    }

    /** The key is (back) in the code. */
    public function markActive(): self
    {
        $this->status = null === $this->value ? TranslationStatus::Missing : TranslationStatus::Translated;
        $this->dateUpd = new \DateTime();

        return $this;
    }

    public function getDateUpd(): ?\DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }
}
