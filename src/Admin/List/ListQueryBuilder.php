<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\List;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Filter\FilterTypeRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

/**
 * Builds the query of a list: entity ("a"), its translation in the current
 * language ("t") when translatable, sorting and submitted filters.
 */
final class ListQueryBuilder
{
    public const ENTITY_ALIAS = 'a';
    public const TRANSLATION_ALIAS = 't';

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly FilterTypeRegistry $filterTypes,
    ) {
    }

    /**
     * @param FormInterface<mixed>|null $filtersForm
     */
    public function create(AdminContext $context, Sorter $sorter, ?FormInterface $filtersForm = null): QueryBuilder
    {
        $config = $context->getEntityConfig();
        $class = $config->class ?? throw new \LogicException(sprintf('Cannot list the virtual entity "%s".', $config->name));

        $manager = $this->registry->getManagerForClass($class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException(sprintf('"%s" is not managed by a Doctrine ORM entity manager.', $class));
        }

        $metadata = $manager->getClassMetadata($class);
        $translatable = $config->isTranslatable();

        $queryBuilder = $manager->createQueryBuilder()
            ->select(self::ENTITY_ALIAS)
            ->from($class, self::ENTITY_ALIAS);

        if ($translatable) {
            $queryBuilder
                ->addSelect(self::TRANSLATION_ALIAS)
                ->leftJoin(self::ENTITY_ALIAS.'.translations', self::TRANSLATION_ALIAS, Join::WITH, self::TRANSLATION_ALIAS.'.language = :dgtx_language')
                ->setParameter('dgtx_language', $context->getLanguage());
        }

        $queryBuilder->orderBy(
            $this->aliasFor($sorter->getProperty(), $metadata, $translatable).'.'.$sorter->getProperty(),
            strtoupper($sorter->getOrderWay())
        );

        if (null !== $filtersForm && $filtersForm->isSubmitted()) {
            foreach ($filtersForm as $child) {
                $filter = $config->list->getFilter($child->getName());
                $value = $child->getData();

                if (null === $filter || !$child->isValid() || null === $value || '' === $value || [] === $value) {
                    continue;
                }

                $alias = $filter->alias ?? $this->aliasFor($filter->name, $metadata, $translatable);
                $this->filterTypes->get($filter->type)->apply($queryBuilder, $filter, $alias, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * Properties that do not belong to the entity are looked up on its translation.
     *
     * @param ClassMetadata<object> $metadata
     */
    private function aliasFor(string $property, ClassMetadata $metadata, bool $translatable): string
    {
        if ($metadata->hasField($property) || $metadata->hasAssociation($property)) {
            return self::ENTITY_ALIAS;
        }

        return $translatable ? self::TRANSLATION_ALIAS : self::ENTITY_ALIAS;
    }
}
