<?php

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\CmsCategoryTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsCategoryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsCategoryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsCategoryTranslation[]    findAll()
 * @method CmsCategoryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsCategoryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsCategoryTranslation::class);
    }

    // /**
    //  * @return CmsCategoryTranslation[] Returns an array of CmsCategoryTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CmsCategoryTranslation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
