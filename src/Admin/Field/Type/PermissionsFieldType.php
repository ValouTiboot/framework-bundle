<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Form\PermissionsType;

/**
 * "permissions": the matrix of entities x permissions of a Role, bound to
 * its "authorization" array. Rows come from the configured admin entities,
 * titled after the admin menu.
 */
final class PermissionsFieldType extends AbstractFieldType
{
    public function __construct(private readonly AdminConfig $config)
    {
    }

    public static function getTypeName(): string
    {
        return 'permissions';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return PermissionsType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $entities = [
            PermissionsType::WILDCARD_ROW => ['label' => 'label.role.allEntities', 'translation_domain' => PermissionsType::LABEL_DOMAIN],
        ];

        foreach ($this->config->getEntities() as $entity) {
            // AdminVoter matches lower-cased entity names
            $entities[strtolower($entity->name)] = [
                'label' => $this->config->getEntityTitle($entity->getSlug()) ?? $entity->name,
                'translation_domain' => false,
            ];
        }

        return ['entities' => $entities, 'required' => false];
    }
}
