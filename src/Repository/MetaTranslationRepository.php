<?php

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\MetaTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetaTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaTranslation[]    findAll()
 * @method MetaTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetaTranslation::class);
    }

    // /**
    //  * @return MetaTranslation[] Returns an array of MetaTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MetaTranslation
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
