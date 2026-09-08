<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Admin\Form\AdminFormBuilder;
use Digitix\FrameworkBundle\DigitixFrameworkBundle;
use Digitix\FrameworkBundle\Finder\TranslationFinder;
use Digitix\FrameworkBundle\Provider\TranslationProvider;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collects the translation keys of a scope (back office, front, emails,
 * theme) and builds one form per translation domain to edit them.
 */
final class TranslationFormFactory
{
    private readonly string $bundlePath;

    public function __construct(
        private readonly TranslationFinder $translationFinder,
        private readonly TranslationProvider $translationProvider,
        private readonly AdminFormBuilder $formBuilder,
    ) {
        $this->bundlePath = DigitixFrameworkBundle::getPathDir();
    }

    public function getProvider(): TranslationProvider
    {
        return $this->translationProvider;
    }

    /**
     * @param array<string, mixed> $selection type, locale, theme
     */
    public function build(array $selection): self
    {
        $type = (string) ($selection['type'] ?? 'bo');
        $locale = (string) ($selection['locale'] ?? 'fr_FR');

        $translations = match ($type) {
            'fo' => array_replace_recursive(
                $this->translationFinder->searchInController('/src/Controller/Front/', '', 'Front.*'),
                $this->translationFinder->searchInConfig('/config/packages/', 'digitix', 'Front.Fields.Label'),
                $this->translationProvider->getTradInFile($this->translationFinder->findFiles('/translations/', $locale.'/Front'), $locale),
            ),
            'email' => array_replace_recursive(
                $this->translationFinder->searchInController('/src/Controller/', '', 'Email.*'),
                $this->translationProvider->getTradInFile($this->translationFinder->findFiles('/translations/', $locale.'/Email'), $locale),
            ),
            'theme' => $this->themeTranslations((string) ($selection['theme'] ?? ''), $locale),
            default => array_replace_recursive(
                $this->translationFinder->searchInController('/src/Controller/Admin/', 'Admin'),
                $this->translationFinder->searchInTemplate('/templates/admin/', ''),
                $this->translationFinder->searchInConfig('/config/packages/', 'digitix'),
                $this->translationProvider->getTradInFile(
                    $this->translationFinder->findFiles('/translations/', $locale.'/Admin')
                    + $this->translationFinder->findFiles($this->bundlePath.'/translations/', $locale.'/Admin'),
                    $locale,
                ),
            ),
        };

        $this->translationProvider->setTranslations($translations);

        return $this;
    }

    /**
     * One form per domain, keyed by domain, bound to the request.
     *
     * @return array<string, FormInterface>
     */
    public function buildForms(Request $request): array
    {
        $forms = [];

        foreach ($this->translationProvider->getTranslations() as $domain => $translations) {
            $definitions = [];

            foreach ($translations as $key => $value) {
                $isHtml = \strlen(strip_tags($key)) !== \strlen($key);

                $definitions[] = [
                    'name' => md5($key),
                    'type' => $isHtml || \strlen($key) > 140 ? TextareaType::class : TextType::class,
                    'options' => [
                        'label' => $key,
                        'translation_domain' => false,
                        'required' => false,
                        'data' => $value,
                        'attr' => $isHtml ? ['class' => 'tinymce'] : [],
                    ],
                ];
            }

            $definitions[] = [
                'name' => 'save',
                'type' => SubmitType::class,
                'options' => ['label' => 'form.default.submit', 'translation_domain' => 'Admin.Form.Default'],
            ];

            $forms[$domain] = $this->formBuilder->createFromDefinitions(str_replace('.', '_', $domain), $definitions, null, [], $request);
        }

        ksort($forms);

        return $forms;
    }

    /**
     * Theme directories under templates/themes, as "name => path" choices
     * (used by the "theme" choice callback of the Translation form).
     *
     * @return array<string, string>
     */
    public static function findThemes(): array
    {
        $themes = [];

        foreach (glob('../templates/themes/*', \GLOB_ONLYDIR) ?: [] as $dir) {
            $themes[basename($dir)] = strchr($dir, '/') ?: $dir;
        }

        return $themes;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function themeTranslations(string $theme, string $locale): array
    {
        if ('' === $theme) {
            return [];
        }

        $themeName = ucfirst(basename($theme));

        return array_replace_recursive(
            $this->translationFinder->searchInTemplate($theme.'/', ''),
            $this->translationProvider->getTradInFile($this->translationFinder->findFiles('/translations/', $locale.'/Theme.'.$themeName), $locale),
        );
    }
}
