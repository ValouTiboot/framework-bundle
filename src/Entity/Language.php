<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Language
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 80)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 2)]
    private ?string $iso = null;

    #[ORM\Column(type: 'string', length: 5)]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $formatDate = null;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $formatDatetime = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = false;

    #[ORM\Column(type: 'boolean')]
    private bool $defaultLanguage = false;

    #[ORM\Column(type: 'boolean')]
    private bool $rtl = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateUpd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso(string $iso): self
    {
        $this->iso = $iso;

        return $this;
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

    public function getFormatDate(): ?string
    {
        return $this->formatDate;
    }

    public function setFormatDate(string $formatDate): self
    {
        $this->formatDate = $formatDate;

        return $this;
    }

    public function getFormatDatetime(): ?string
    {
        return $this->formatDatetime;
    }

    public function setFormatDatetime(string $formatDatetime): self
    {
        $this->formatDatetime = $formatDatetime;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDefaultLanguage(): bool
    {
        return $this->defaultLanguage;
    }

    public function setDefaultLanguage(bool $defaultLanguage): self
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    public function getRtl(): bool
    {
        return $this->rtl;
    }

    public function setRtl(bool $rtl): self
    {
        $this->rtl = $rtl;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

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
