<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Security;

use Digitix\FrameworkBundle\Admin\Config\EntityConfig;
use Digitix\FrameworkBundle\Admin\Config\FormConfig;
use Digitix\FrameworkBundle\Admin\Config\ListConfig;
use Digitix\FrameworkBundle\Admin\Config\ViewConfig;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Admin\Security\AdminVoter;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Entity\Role;
use Digitix\FrameworkBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class AdminVoterTest extends TestCase
{
    public function testSuperAdminMayDoEverything(): void
    {
        $voter = new AdminVoter($this->security(true));
        $token = $this->token((new Role())->setName('SuperAdmin'));

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $this->entity('Cms'), [AdminPermission::DELETE]));
    }

    public function testRoleAuthorizationIsHonoured(): void
    {
        $voter = new AdminVoter($this->security(false));
        $token = $this->token((new Role())->setName('Editor')->setAuthorization([
            '*' => ['read'],
            'cms' => ['create', 'edit'],
            'user' => ['*'],
        ]));

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $this->entity('Menu'), [AdminPermission::READ]), 'wildcard entity');
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, $this->entity('Menu'), [AdminPermission::EDIT]));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $this->entity('Cms'), [AdminPermission::EDIT]));
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, $this->entity('Cms'), [AdminPermission::DELETE]));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $this->entity('User'), [AdminPermission::DELETE]), 'wildcard permission');
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, 'cms', [AdminPermission::CREATE]), 'entity name as subject');
        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, $this->entity('Cms'), ['ROLE_ADMIN']), 'other attributes are ignored');
    }

    public function testPermissionHelpers(): void
    {
        self::assertSame('delete', AdminPermission::shortName(AdminPermission::DELETE));
        self::assertSame(AdminPermission::VIEW, AdminPermission::forAction('view_entity'));
        self::assertSame(AdminPermission::EDIT, AdminPermission::forAction('edit'));
        self::assertSame(AdminPermission::READ, AdminPermission::forAction('anything'));
    }

    private function security(bool $superAdmin): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(static fn (mixed $attribute) => $superAdmin && AdminVoter::SUPER_ADMIN_ROLE === $attribute);

        return $security;
    }

    private function token(Role $role): UsernamePasswordToken
    {
        $user = (new User())->setEmail('a@b.c')->setRole($role);

        return new UsernamePasswordToken($user, 'dgtx_admin', $user->getRoles());
    }

    private function entity(string $name): EntityConfig
    {
        return new EntityConfig(
            $name,
            null,
            AdminController::class,
            new ListConfig(null, false, false, 30, [], [], [], [], []),
            new FormConfig(null, true, true, 'Admin.Fields.Label', []),
            new ViewConfig(null),
        );
    }
}
