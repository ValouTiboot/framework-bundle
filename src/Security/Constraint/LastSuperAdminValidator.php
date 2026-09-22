<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Security\Constraint;

use Digitix\FrameworkBundle\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LastSuperAdminValidator extends ConstraintValidator
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof LastSuperAdmin) {
            throw new UnexpectedTypeException($constraint, LastSuperAdmin::class);
        }

        if (!$value instanceof Role || $value->isSuperAdmin() || null === $value->getId()) {
            return;
        }

        // other super admin roles, this one excluded (its stored state does not matter)
        $others = (int) $this->registry->getRepository(Role::class)->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.superAdmin = true')
            ->andWhere('r.id != :id')
            ->setParameter('id', $value->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if (0 === $others && $this->wasSuperAdmin($value)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('superAdmin')
                ->addViolation();
        }
    }

    /** Stored value of the flag, before the form changed it. */
    private function wasSuperAdmin(Role $role): bool
    {
        $manager = $this->registry->getManagerForClass(Role::class);

        if (!$manager instanceof \Doctrine\ORM\EntityManagerInterface) {
            return false;
        }

        $original = $manager->getUnitOfWork()->getOriginalEntityData($role);

        return (bool) ($original['superAdmin'] ?? false);
    }
}
