<?php

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\CmsCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsCategory[]    findAll()
 * @method CmsCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsCategory::class);
    }

    // /**
    //  * @return CmsCategory[] Returns an array of CmsCategory objects
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
    public function findOneBySomeField($value): ?CmsCategory
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
