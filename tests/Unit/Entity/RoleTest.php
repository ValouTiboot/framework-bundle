<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Entity;

use Digitix\FrameworkBundle\Entity\Role;
use Digitix\FrameworkBundle\Entity\User;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    public function testTheCodeIsDerivedOnceFromTheFirstName(): void
    {
        $role = (new Role())->setName('Chef de projet');
        self::assertSame('CHEF_DE_PROJET', $role->getCode());
        self::assertSame('ROLE_CHEF_DE_PROJET', $role->getSecurityRole());

        $role->setName('Responsable éditorial');
        self::assertSame('CHEF_DE_PROJET', $role->getCode(), 'renaming never changes the code');

        self::assertSame('SUPERADMIN', Role::codeFrom('SuperAdmin'), 'same code as the historical derivation');
        self::assertSame('ROLE', Role::codeFrom('***'));
    }

    public function testSymfonyRolesComeFromTheCodeAndTheFlag(): void
    {
        $editor = (new User())->setRole((new Role())->setName('Editor'));
        self::assertSame(['ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_USER'], $editor->getRoles());

        $superAdmin = (new User())->setRole((new Role())->setName('Boss')->setSuperAdmin(true));
        self::assertSame(['ROLE_ADMIN', 'ROLE_BOSS', 'ROLE_SUPERADMIN', 'ROLE_USER'], $superAdmin->getRoles(), 'full access does not depend on the name');

        self::assertSame(['ROLE_USER'], (new User())->getRoles(), 'no role, no admin');
    }
}
