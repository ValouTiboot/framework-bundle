<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Role;
use Digitix\FrameworkBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Roles: the generic CRUD, plus two guards on delete: a role still given
 * to users, or the last super admin role, cannot be removed.
 */
class AdminRoleController extends AdminController
{
    public function delete(AdminContext $context): RedirectResponse
    {
        $this->assertGranted(AdminPermission::DELETE, $context);

        try {
            $role = $context->getEntity();
        } catch (NotFoundHttpException) {
            $role = null;
        }

        if ($role instanceof Role) {
            $users = $this->doctrine()->getRepository(User::class)->count(['role' => $role]);
            if ($users > 0) {
                $this->addFlash('danger', $this->trans('This role is still given to %count% user(s), reassign them first.', ['%count%' => $users], 'Admin.Message.Error'));

                return $this->redirectToList($context);
            }

            if ($role->isSuperAdmin() && 1 === $this->doctrine()->getRepository(Role::class)->count(['superAdmin' => true])) {
                $this->addFlash('danger', $this->trans('This is the last super admin role, it cannot be deleted.', [], 'Admin.Message.Error'));

                return $this->redirectToList($context);
            }
        }

        return parent::delete($context);
    }
}
