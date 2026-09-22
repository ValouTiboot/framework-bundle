<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\EventListener;

use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

/**
 * Tells every loaded translatable entity which language is current, so that
 * its magic accessors ($cms->name, $cms->getName()) return the right
 * translation instead of a hard-coded one.
 */
#[AsDoctrineListener(event: Events::postLoad)]
final class TranslatableLanguageListener
{
    public function __construct(private readonly LanguageProvider $languages)
    {
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Translatable) {
            $entity->setCurrentLanguageId($this->languages->getDefaultLanguageId());
        }
    }
}
