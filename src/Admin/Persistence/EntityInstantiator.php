<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Persistence;

use Digitix\FrameworkBundle\Admin\Config\EntityConfig;
use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Loads an existing record or creates a blank one for the managed entity.
 */
final class EntityInstantiator
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly LanguageProvider $languages,
    ) {
    }

    /**
     * @throws NotFoundHttpException when an id is given but no record matches
     */
    public function instantiate(EntityConfig $config, ?int $id): ?object
    {
        if ($config->isVirtual()) {
            return null;
        }

        $class = $config->class;

        if (null !== $id) {
            $entity = $this->registry->getManagerForClass($class)?->find($class, $id);

            if (null === $entity) {
                throw new NotFoundHttpException(sprintf('No "%s" with id %d.', $config->name, $id));
            }
        } else {
            $entity = new $class();

            if (null !== ($translationClass = $config->getTranslationClass())) {
                $this->initialiseTranslations($entity, $translationClass);
            }
        }

        if ($entity instanceof Translatable) {
            $entity->setCurrentLanguageId($this->languages->getDefaultLanguage()->getId());
        }

        return $entity;
    }

    /**
     * A new translatable record gets one empty translation per active
     * language, default language first, so the form can render them all.
     */
    private function initialiseTranslations(object $entity, string $translationClass): void
    {
        foreach ($this->languages->getActiveLanguages() as $language) {
            $translation = new $translationClass();
            $translation->setLanguage($language);
            $entity->addTranslation($translation);
        }
    }
}
