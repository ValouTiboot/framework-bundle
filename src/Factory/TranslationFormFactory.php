<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Field\TextField;
use Digitix\FrameworkBundle\Field\TextareaField;
use Digitix\FrameworkBundle\Field\SubmitField;
use Digitix\FrameworkBundle\Field\FieldData;
use Digitix\FrameworkBundle\Finder\TranslationFinder;
use Digitix\FrameworkBundle\Form\Type\FormType;
use Digitix\FrameworkBundle\Provider\TranslationProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Forms;

final class TranslationFormFactory
{
	private $translationFinder;
	private $translationProvider;
	private $fields;

	public function __construct(TranslationFinder $translationFinder, TranslationProvider $translationProvider)
	{
		$this->translationFinder = $translationFinder;
		$this->translationProvider = $translationProvider;
	}

	public function getProvider()
	{
		return $this->translationProvider;
	}

	public function build($data = '')
	{
		if ($data['type'] == 'bo') {
			$adminControllerTrads = $this->translationFinder->searchInController('/src/Controller/Admin/', 'Admin');
			$adminTemplateTrads = $this->translationFinder->searchInTemplate('/templates/admin/', '');
			$adminFieldsTrads = $this->translationFinder->searchInConfig('/config/dgtx/', 'admin_entities');

			$translationFiles = $this->translationFinder->findFiles('/translations/', $data['locale'].'/Admin');
			$bundleTranslationFiles = $this->translationFinder->findFiles('/vendor/digitix/framework-bundle/src/Resources/translations/', $data['locale'].'/Admin');
			$translationTrads = $this->translationProvider->getTradInFile($translationFiles + $bundleTranslationFiles, $data['locale']);

			$this->translationProvider->setTranslations(
				array_replace_recursive(
					$adminControllerTrads,
					$adminTemplateTrads,
					$adminFieldsTrads,
					$translationTrads
				)
			);
		} else if ($data['type'] == 'fo') {
			$frontControllerTrads = $this->translationFinder->searchInController('/src/Controller/Front/', '', 'Front.*');
			$frontFieldsTrads = $this->translationFinder->searchInConfig('/config/dgtx/', 'front_entities', 'Front.Fields.Label');
			$translationFiles = $this->translationFinder->findFiles('/translations/', $data['locale'].'/Front');
			$translationTrads = $this->translationProvider->getTradInFile($translationFiles, $data['locale']);

			$this->translationProvider->setTranslations(
				array_replace_recursive(
					$frontControllerTrads,
					$frontFieldsTrads,
					$translationTrads
				)
			);
		}
		else if ($data['type'] == 'email') {
			$frontControllerTrads = $this->translationFinder->searchInController('/src/Controller/', '', 'Email.*');
			$translationFiles = $this->translationFinder->findFiles('/translations/', $data['locale'].'/Email');
			$translationTrads = $this->translationProvider->getTradInFile($translationFiles, $data['locale']);

			$this->translationProvider->setTranslations(
				array_replace_recursive(
					$frontControllerTrads,
					$translationTrads
				)
			);
		} else if ($data['type'] == 'theme') {
			$themeName = ucfirst(substr(strrchr($data['theme'], '/'), 1));
			$frontTemplateTrads = $this->translationFinder->searchInTemplate($data['theme'].'/', '');
			$translationFiles = $this->translationFinder->findFiles('/translations/', $data['locale'].'/Theme.'.$themeName);
			$translationTrads = $this->translationProvider->getTradInFile($translationFiles, $data['locale']);

			$this->translationProvider->setTranslations(
				array_replace_recursive(
					$frontTemplateTrads,
					$translationTrads
				)
			);
		}

		return $this;
	}

	public function buildFields()
	{
		$formFields = [];
		foreach ($this->getProvider()->getTranslations() as $domain => $translations) {
			$buildFields = [];

			foreach ($translations as $name => $value) {
				$field = [
					'label' => $name,
					'required' => false,
					'data' => $value,
				];

				if (strlen(strip_tags($name)) != strlen($name)) {
					$fieldType = TextareaField::class;
					$field['class'] = 'tinymce';
				} else if (strlen($name) > 140) {
					$fieldType = TextareaField::class;
				} else {
					$fieldType = TextField::class;
				}

				$name = md5($name);
				$buildFields[$name] = $fieldType::getInstance($name, $field);
			}
			$buildFields['save'] = SubmitField::getInstance();
			$formFields[$domain] = new ArrayCollection($buildFields);
		}

		$this->fields = new ArrayCollection($formFields);

		return $this;
	}

	public function buildForms($overrideOptions = []): array
	{
        $forms = [];
        foreach ($this->fields as $domain => $fields) {
			$options = [
	            'method' => 'POST',
	            'fields' => $fields,
	        ];

			$form = Forms::createFormFactoryBuilder()->getFormFactory()->createNamed(
				str_replace('.', '_', $domain),
				FormType::class,
				// (new FieldData($fields))->toArray(),
				null,
				array_merge($options, $overrideOptions)
			);

        	$forms[$domain] = $form->handleRequest();
        }

        ksort($forms);
        return $forms;
	}

	// private function buildTree($flat, $key = 0)
	// {
	// 	$tree = '';
	// 	for ($i = count($flat)-1; $i >= 0; $i--)
	// 	{
	// 		if (end($flat) === $flat[$i])
	// 			$tree = [$flat[$i] => ''];
	// 		else
	// 		{
	// 			$tree[$flat[$i]] = $tree;
	// 			unset($tree[$flat[$i+1]]);
	// 		}
	// 	}

	// 	return $tree;
	// }

	public function findThemes(): array
    {
        $themes = [];
        $tmpDir = glob('../templates/themes/*', GLOB_ONLYDIR);

        if (count($tmpDir)) {
            foreach ($tmpDir as $dir) {
				$themes[substr(strrchr($dir, '/'), 1)] = strchr($dir, '/');
			}
        }

        return $themes;
    }
}
