<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Upload;

use Digitix\FrameworkBundle\Admin\Config\FormConfig;
use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Moves the files submitted through "file" fields under
 * public/uploads/{entity}/{field}/ and writes the stored file name into the
 * matching entity property.
 */
final class UploadHandler
{
    public function __construct(
        private readonly string $projectDir,
        private readonly SluggerInterface $slugger,
        private readonly PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function handle(AdminContext $context, FormInterface $form, ?FormConfig $formConfig = null): void
    {
        $entity = $context->getEntity();
        if (null === $entity) {
            return;
        }

        $formConfig ??= $context->getEntityConfig()->form;

        foreach ($formConfig->getFieldsOfType('file') as $field) {
            if (!$form->has($field->name)) {
                continue;
            }

            $file = $form->get($field->name)->getData();
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $directory = $this->directory($context->getEntitySlug(), $field->name);
            $fileName = $this->fileName($file);

            $file->move($directory, $fileName);
            $this->propertyAccessor->setValue($entity, $field->property, $fileName);
        }
    }

    public function directory(string $entitySlug, string $fieldName): string
    {
        $directory = sprintf('%s/public/uploads/%s/%s', $this->projectDir, $entitySlug, $fieldName);

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create upload directory "%s".', $directory));
        }

        return $directory;
    }

    private function fileName(UploadedFile $file): string
    {
        $base = $this->slugger->slug(pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME))->lower();
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension() ?: 'bin';

        return sprintf('%s-%s.%s', $base, substr(bin2hex(random_bytes(4)), 0, 8), $extension);
    }
}
