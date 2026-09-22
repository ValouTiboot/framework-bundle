<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Form;

use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Permission matrix of a Role: one row per admin entity (plus "every
 * entity"), one checkbox per permission (plus "all of them"). Bound to the
 * Role::authorization array read by AdminVoter:
 *
 *   { "*": ["read"], "cms": ["read", "create", "edit"], "user": ["*"] }
 *
 * The wildcard entity is the "_all" child, the wildcard permission the
 * "all" choice: form names cannot contain "*".
 *
 * @extends AbstractType<array<string, string[]>>
 */
final class PermissionsType extends AbstractType
{
    public const WILDCARD = '*';
    public const WILDCARD_ROW = '_all';
    public const WILDCARD_CHOICE = 'all';
    public const LABEL_DOMAIN = 'Admin.Fields.Label';

    /** @return string[] permission short names, in display order */
    public static function permissions(): array
    {
        return array_map([AdminPermission::class, 'shortName'], AdminPermission::ALL);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        foreach ([...self::permissions(), self::WILDCARD] as $permission) {
            $choices[self::choiceName($permission)] = $permission;
        }

        /** @var array<string, array{label: string, translation_domain: string|false}> $entities */
        $entities = $options['entities'];

        foreach ($entities as $name => $entity) {
            $builder->add($name, ChoiceType::class, [
                'label' => $entity['label'],
                'translation_domain' => $entity['translation_domain'],
                'choices' => $choices,
                'choice_name' => static fn (string $permission): string => self::choiceName($permission),
                'choice_label' => static fn (string $permission): string => 'label.role.permission.'.self::choiceName($permission),
                'choice_translation_domain' => self::LABEL_DOMAIN,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ]);
        }

        $builder->addModelTransformer(new CallbackTransformer(
            static function (mixed $authorization) use ($entities): array {
                $authorization = \is_array($authorization) ? array_change_key_case($authorization) : [];
                $rows = [];

                foreach (array_keys($entities) as $name) {
                    $permissions = $authorization[self::WILDCARD_ROW === $name ? self::WILDCARD : $name] ?? [];
                    $rows[$name] = array_values(array_filter((array) $permissions, 'is_string'));
                }

                return $rows;
            },
            static function (mixed $rows): array {
                $authorization = [];

                foreach (\is_array($rows) ? $rows : [] as $name => $permissions) {
                    $permissions = array_values(array_unique(array_filter((array) $permissions, 'is_string')));

                    if ([] === $permissions) {
                        continue;
                    }

                    if (\in_array(self::WILDCARD, $permissions, true)) {
                        $permissions = [self::WILDCARD];
                    }

                    $authorization[self::WILDCARD_ROW === $name ? self::WILDCARD : (string) $name] = $permissions;
                }

                return $authorization;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('entities')
            ->setAllowedTypes('entities', 'array')
            ->setDefaults([
                'required' => false,
                'label' => 'label.role.authorization',
                'translation_domain' => self::LABEL_DOMAIN,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'dgtx_permissions';
    }

    public static function choiceName(string $permission): string
    {
        return self::WILDCARD === $permission ? self::WILDCARD_CHOICE : $permission;
    }
}
