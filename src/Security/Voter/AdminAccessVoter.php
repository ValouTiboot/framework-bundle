<?php

namespace Digitix\FrameworkBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class AdminAccessVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['create', 'read', 'edit', 'delete']);
            // && $subject instanceof \Digitix\FrameworkBundle\Entity\Admin;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        dump('do check permissions user');
        // que  moi en super admin par la conf si possible et granted super admin only
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        dump($user->getRole());
        // ... (check conditions and return true to grant permission on role json) ...
        switch ($attribute) {
            case 'create':
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'read':
                return true;
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'edit':
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'delete':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
