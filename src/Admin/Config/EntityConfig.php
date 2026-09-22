<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

/**
 * Everything the admin knows about one managed "entity".
 *
 * An entity may be virtual (no Doctrine class behind it): Dashboard,
 * Parameter, Performance, Translation... In that case $class is null and the
 * controller is expected to provide its own behaviour.
 */
final class EntityConfig
{
    /**
     * @param class-string|null $class
     * @param class-string      $controller
     */
    public function __construct(
        /** Canonical name, as written in the YAML key (e.g. "CmsCategory"). */
        public readonly string $name,
        /** Fully qualified Doctrine entity class, or null for virtual entities. */
        public readonly ?string $class,
        /** Fully qualified controller class handling this entity. */
        public readonly string $controller,
        public readonly ListConfig $list,
        public readonly FormConfig $form,
        public readonly ViewConfig $view,
    ) {
    }

    /** URL segment used in admin routes ("cmsCategory"). */
    public function getSlug(): string
    {
        return lcfirst($this->name);
    }

    public function isVirtual(): bool
    {
        return null === $this->class;
    }

    /**
     * Convention: a translatable entity has a sibling "<Class>Translation" entity.
     *
     * @return class-string|null
     */
    public function getTranslationClass(): ?string
    {
        if (null === $this->class) {
            return null;
        }

        $translationClass = $this->class.'Translation';

        return class_exists($translationClass) ? $translationClass : null;
    }

    public function isTranslatable(): bool
    {
        return null !== $this->getTranslationClass();
    }
}
