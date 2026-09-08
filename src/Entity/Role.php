<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin role. The Symfony role is derived from the name
 * ("SuperAdmin" => ROLE_SUPERADMIN, see User::getRoles()); "authorization"
 * holds per-entity permissions read by AdminVoter.
 */
#[ORM\Entity]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 80)]
    private ?string $name = null;

    /** @var array<string, string[]> */
    #[ORM\Column(type: 'json')]
    private array $authorization = [];

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

    /** @return array<string, string[]> */
    public function getAuthorization(): array
    {
        return $this->authorization;
    }

    /** @param array<string, string[]> $authorization */
    public function setAuthorization(array $authorization): self
    {
        $this->authorization = $authorization;

        return $this;
    }
}
