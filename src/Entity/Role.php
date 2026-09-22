<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity;

use Digitix\FrameworkBundle\Security\Constraint\LastSuperAdmin;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Admin role.
 *
 *  - "name" is what people see and may be renamed freely;
 *  - "code" is a technical identifier generated once from the initial name
 *    and never changed: it backs the Symfony role ROLE_<CODE>;
 *  - "superAdmin" grants every permission (AdminVoter), whatever the matrix;
 *  - "authorization" holds the per-entity permissions of the other roles.
 */
#[ORM\Entity]
#[UniqueEntity('name')]
#[UniqueEntity('code')]
#[LastSuperAdmin]
class Role
{
    public const SUPER_ADMIN_ROLE = 'ROLE_SUPERADMIN';
    public const ADMIN_ROLE = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 40, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[A-Z0-9_]+$/', message: 'Use capital letters, digits and "_" only.')]
    private ?string $code = null;

    #[ORM\Column(name: 'super_admin', type: 'boolean', options: ['default' => false])]
    private bool $superAdmin = false;

    /** @var array<string, string[]> */
    #[ORM\Column(type: 'json')]
    private array $authorization = [];

    /** Technical code of a name: "Chef de projet" => "CHEF_DE_PROJET". */
    public static function codeFrom(string $name): string
    {
        $code = strtoupper((string) preg_replace('/[^A-Za-z0-9]+/', '_', trim($name)));

        return trim($code, '_') ?: 'ROLE';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /** The code is derived from the first name given, then stays as it is. */
    public function setName(string $name): self
    {
        $this->name = $name;
        $this->code ??= self::codeFrom($name);

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /** Only meaningful before the first save (imports, fixtures). */
    public function setCode(?string $code): self
    {
        $this->code = null === $code || '' === trim($code) ? null : self::codeFrom($code);

        return $this;
    }

    /** Symfony role backing this admin role: ROLE_<CODE>. */
    public function getSecurityRole(): string
    {
        return 'ROLE_'.($this->code ?? self::codeFrom((string) $this->name));
    }

    public function isSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    public function getSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(bool $superAdmin): self
    {
        $this->superAdmin = $superAdmin;

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
