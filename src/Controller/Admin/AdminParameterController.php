<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Configuration;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Parameter" virtual entity: one form whose fields are stored as
 * Configuration rows (name => value).
 */
class AdminParameterController extends AdminController
{
    public function create(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $form = $this->forms()->createForm($context, [], $this->currentValues($context));

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveConfiguration($form->getData());
            $this->addFlash('success', $this->trans('Succesfully updated.'));

            return $this->redirectToRoute('dgtx_admin_entity_create', ['entityName' => $context->getEntitySlug()]);
        }

        return $this->renderAdmin(
            $this->templates()->form($context->getEntityConfig()),
            $this->formView()->build($context, $form)
        );
    }

    /**
     * @return array<string, mixed> field name => current Configuration value
     */
    protected function currentValues(AdminContext $context): array
    {
        $values = [];

        foreach ($context->getEntityConfig()->form->fields as $field) {
            if (\in_array($field->type, ['submit', 'button'], true)) {
                continue;
            }

            $value = $this->configuration()->getValue($field->name);

            $values[$field->name] = 'bool' === $field->type
                ? filter_var($value, \FILTER_VALIDATE_BOOLEAN)
                : $value;
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $data submitted form data
     */
    protected function saveConfiguration(array $data): void
    {
        $rows = [];

        foreach ($data as $name => $value) {
            $row = $this->configuration()->get($name) ?? (new Configuration())->setName($name);

            $row->setValue(match (true) {
                \is_bool($value) => $value ? '1' : '0',
                null === $value => null,
                default => (string) $value,
            });

            $rows[] = $row;
        }

        $this->persister()->saveAll($rows, false);
        $this->configuration()->reset();
    }
}
