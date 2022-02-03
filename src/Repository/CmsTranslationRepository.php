<?php

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsTranslation[]    findAll()
 * @method CmsTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsTranslation::class);
    }

    // /**
    //  * @return CmsTranslation[] Returns an array of CmsTranslation objects
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
    public function findOneBySomeField($value): ?CmsTranslation
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
