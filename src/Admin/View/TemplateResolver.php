<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\View;

use Digitix\FrameworkBundle\Admin\Config\EntityConfig;

/**
 * Picks the Twig template of a page: the one configured for the entity, or
 * the bundle default. Configured names may omit the ".twig" suffix.
 */
final class TemplateResolver
{
    public const LIST = '@DigitixFramework/admin/helper/list/list.twig';
    public const FORM = '@DigitixFramework/admin/helper/form/_partials/form.html.twig';
    public const VIEW = '@DigitixFramework/admin/helper/view/view.html.twig';

    public function list(EntityConfig $entity): string
    {
        return self::normalize($entity->list->template) ?? self::LIST;
    }

    public function form(EntityConfig $entity): string
    {
        return self::normalize($entity->form->template) ?? self::FORM;
    }

    public function view(EntityConfig $entity): string
    {
        return self::normalize($entity->view->template) ?? self::VIEW;
    }

    public static function normalize(?string $template): ?string
    {
        if (null === $template || '' === trim($template)) {
            return null;
        }

        return str_ends_with($template, '.twig') ? $template : $template.'.twig';
    }
}
