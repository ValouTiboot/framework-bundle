<?php

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Sorter\Sorter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class EntityRepository extends ServiceEntityRepository
{
	private $context;
	private $entityName;
	private $entityFqcn;

	public function __construct(ManagerRegistry $registry, ?ContextProvider $context = null, string $fqcn = null)
    {
        if ($context !== null) {
        	$this->context = $context->getContext();
        	$this->entityFqcn = $this->context->getEntity()->getFqcn();
        }  else if ($fqcn !== null) {
            $this->entityFqcn = $fqcn;
        }

        parent::__construct($registry, $this->entityFqcn);
    }

    public function buildQuery($fields, ArrayCollection $search, Sorter $sorter)
    {
        /**
         * TODO
         * select fields
         */
        $select = 'a.'.implode(', a.', array_keys($fields));
        // dump($select);
    	$queryBuilder = $this->getEntityManager()->createQueryBuilder();

    	$queryBuilder
    		->select('a')
    		->from($this->entityFqcn, 'a')
            ->orderBy('a.'.$sorter->getOrderBy(), $sorter->getOrderWay());

        if (class_exists($this->entityFqcn.'Translation')) {
            $queryBuilder->leftJoin('a.translations', 't');
            $queryBuilder->where('t.language='. $this->context->getLanguage()->getId());
        }

        if ($search->count()) {
            $this->addWhereClause($queryBuilder, $search);
        }

    	return $queryBuilder;
    }

    public function addWhereClause($queryBuilder, $search)
    {
        /* TODO
        * OR (XOR XAND)
        * make it dynamic
        */
        foreach ($search as $s) {
            $queryBuilder->andWhere($s->getQueryString())->setParameter($s->getNameParameter(), $s->getValueParameter());
        }
    }
}
