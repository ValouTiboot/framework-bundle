<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Config\EntityConfig;
use Digitix\FrameworkBundle\Admin\Config\FormConfig;
use Digitix\FrameworkBundle\Admin\Field\Type\SubmitFieldType;
use Digitix\FrameworkBundle\Admin\Filter\AbstractFilterType;
use Digitix\FrameworkBundle\Menu\MenuSourceProvider;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Translation keys declared by the "digitix_framework" configuration: field
 * labels and helps, filter labels, choice labels, header links... They never
 * appear in a trans() call, so the code extractors cannot see them.
 *
 * Domains follow what the templates and field types actually use.
 */
final class ConfigKeyExtractor
{
    /** Domain hard-coded by the list templates for column headers and header links. */
    public const LIST_DOMAIN = 'Admin.Fields.Label';

    /**
     * @param array<int, array{route: string, label: string, params?: array<string, mixed>}> $menuPages "digitix_framework.menu.pages"
     */
    public function __construct(
        private readonly AdminConfig $config,
        private readonly array $menuPages = [],
    ) {
    }

    public function extract(MessageCatalogue $catalogue): void
    {
        foreach ($this->config->getEntities() as $entity) {
            $this->extractList($entity, $catalogue);
            $this->extractForm($entity->form, $catalogue);
        }

        foreach ($this->config->getFrontForms() as $form) {
            $this->extractForm($form, $catalogue);
        }

        foreach ($this->menuPages as $page) {
            $this->add($catalogue, MenuSourceProvider::LABEL_DOMAIN, $page['label']);
        }
    }

    private function extractList(EntityConfig $entity, MessageCatalogue $catalogue): void
    {
        $list = $entity->list;

        foreach ($list->fields as $field) {
            $this->add($catalogue, self::LIST_DOMAIN, $field->label);

            // choice columns are displayed through "label.<entity>.<value>"
            if ('choice' === $field->type) {
                $formField = null;
                foreach ($entity->form->fields as $candidate) {
                    if ($candidate->property === $field->property) {
                        $formField = $candidate;
                        break;
                    }
                }

                foreach ((array) ($formField?->get('choice') ?? []) as $value) {
                    if (\is_scalar($value)) {
                        $this->add($catalogue, self::LIST_DOMAIN, sprintf('label.%s.%s', $entity->getSlug(), $value));
                    }
                }
            }
        }

        foreach ($list->headerLinks as $link) {
            $this->add($catalogue, self::LIST_DOMAIN, isset($link['name']) ? (string) $link['name'] : null);
        }

        foreach ($list->filters as $filter) {
            $domain = AbstractFilterType::TRANSLATION_DOMAIN;
            $this->add($catalogue, $domain, $filter->label);

            if ('choice' === $filter->type) {
                foreach (array_keys((array) $filter->get('choice', [])) as $label) {
                    $this->add($catalogue, $domain, (string) $label);
                }
            } elseif ('bool' === $filter->type) {
                $this->addBoolLabels($catalogue, $domain);
            }
        }
    }

    private function extractForm(FormConfig $form, MessageCatalogue $catalogue): void
    {
        $domain = $form->translationDomain;

        foreach ($form->fields as $field) {
            if (\in_array($field->type, ['submit', 'button'], true)) {
                $this->add($catalogue, $domain, $field->label ?? SubmitFieldType::DEFAULT_LABEL);
                continue;
            }

            $this->add($catalogue, $domain, $field->label);
            $this->add($catalogue, $domain, $field->has('help') ? (string) $field->get('help') : null);

            $attr = (array) $field->get('attr', []);
            foreach (['placeholder', 'title'] as $translatedAttribute) {
                $this->add($catalogue, $domain, isset($attr[$translatedAttribute]) ? (string) $attr[$translatedAttribute] : null);
            }

            if ('choice' === $field->type) {
                foreach (array_keys((array) $field->get('choice', [])) as $label) {
                    $this->add($catalogue, $domain, (string) $label);
                }
            } elseif ('bool' === $field->type) {
                $this->addBoolLabels($catalogue, $domain);
            }
        }

        if ($form->hasAutoSubmitButton) {
            $this->add($catalogue, SubmitFieldType::DEFAULT_DOMAIN, SubmitFieldType::DEFAULT_LABEL);
        }
    }

    private function addBoolLabels(MessageCatalogue $catalogue, string $domain): void
    {
        $this->add($catalogue, $domain, 'label.default.yes');
        $this->add($catalogue, $domain, 'label.default.no');
    }

    private function add(MessageCatalogue $catalogue, string $domain, ?string $key): void
    {
        if (null === $key || '' === $key || $catalogue->defines($key, $domain)) {
            return;
        }

        $catalogue->set($key, '', $domain);
    }
}
