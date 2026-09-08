<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Form\AdminFormBuilder;
use Digitix\FrameworkBundle\Admin\Persistence\EntityPersister;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Admin\Upload\UploadHandler;
use Digitix\FrameworkBundle\Admin\View\FormViewBuilder;
use Digitix\FrameworkBundle\Admin\View\ListViewBuilder;
use Digitix\FrameworkBundle\Admin\View\TemplateResolver;
use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Generic CRUD controller. Every admin route targets it; AdminRequestListener
 * redirects to the entity's own controller when one exists (by configuration
 * or by convention), which only has to override what differs.
 *
 * Actions receive the AdminContext of the request: entity configuration,
 * loaded record, language...
 */
class AdminController extends AbstractController
{
    public const MESSAGE_DOMAIN = 'Admin.Message.Success';

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            AdminConfig::class,
            AdminFormBuilder::class,
            ListViewBuilder::class,
            FormViewBuilder::class,
            TemplateResolver::class,
            EntityPersister::class,
            UploadHandler::class,
            ConfigurationProvider::class,
            TranslatorInterface::class,
            ManagerRegistry::class,
        ]);
    }

    public function view(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::VIEW, $context);

        return $this->renderAdmin($this->templates()->view($context->getEntityConfig()), $this->baseVars($context));
    }

    public function viewEntity(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::VIEW, $context);

        return $this->renderAdmin(
            $this->templates()->view($context->getEntityConfig()),
            $this->baseVars($context) + ['entity' => $context->getEntity()]
        );
    }

    public function read(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::READ, $context);

        return $this->renderAdmin($this->templates()->list($context->getEntityConfig()), $this->listView()->build($context));
    }

    public function create(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::CREATE, $context);

        $form = $this->forms()->createForm($context, ['validation_groups' => ['Default', 'Create']]);

        return $this->handleForm($context, $form, 'Entity successfuly added.');
    }

    public function edit(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $form = $this->forms()->createForm($context);

        return $this->handleForm($context, $form, 'Entity successfuly updated.');
    }

    public function delete(AdminContext $context): RedirectResponse
    {
        $this->assertGranted(AdminPermission::DELETE, $context);

        $token = (string) $context->getRequest()->request->get('_token');

        if (!$this->isCsrfTokenValid(self::deleteTokenId($context->getEntityId()), $token)) {
            $this->addFlash('danger', $this->trans('Invalid security token, please try again.', 'Admin.Message.Error'));

            return $this->redirectToList($context);
        }

        try {
            $entity = $context->getEntity();
        } catch (NotFoundHttpException) {
            $entity = null;
        }

        if (null === $entity) {
            $this->addFlash('info', $this->trans('This entity does not exist anymore.', 'Admin.Message.Info'));
        } else {
            $this->persister()->remove($entity);
            $this->addFlash('success', $this->trans('Entity successfuly deleted.'));
        }

        return $this->redirectToList($context);
    }

    /**
     * Receives "{entitySlug}[]=id" in display order (jQuery UI sortable
     * serialize) and rewrites the "position" property accordingly.
     */
    public function ajaxSortable(AdminContext $context): JsonResponse
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $ids = $context->getRequest()->request->all()[$context->getEntitySlug()] ?? null;
        $class = $context->getEntityClass();

        if (!\is_array($ids) || null === $class) {
            return new JsonResponse(['success' => false]);
        }

        $repository = $this->doctrine()->getRepository($class);
        $items = [];

        foreach (array_values($ids) as $position => $id) {
            $item = $repository->find((int) $id);

            if (null === $item || !method_exists($item, 'setPosition')) {
                continue;
            }

            $item->setPosition($position + 1);
            $items[] = $item;
        }

        $this->persister()->saveAll($items);

        return new JsonResponse(['success' => true]);
    }

    public static function deleteTokenId(?int $entityId): string
    {
        return 'dgtx_delete_'.$entityId;
    }

    // --- helpers for subclasses --------------------------------------------------------

    /**
     * Saves the entity when the form is valid and redirects to the list,
     * otherwise renders the form page.
     *
     * @param FormInterface<mixed> $form
     */
    protected function handleForm(AdminContext $context, FormInterface $form, string $successMessage): Response
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== ($entity = $context->getEntity())) {
                $this->uploads()->handle($context, $form);
                $this->persister()->save($entity);
            }

            $this->addFlash('success', $this->trans($successMessage));

            return $this->redirectToList($context);
        }

        return $this->renderAdmin(
            $this->templates()->form($context->getEntityConfig()),
            $this->formView()->build($context, $form)
        );
    }

    /**
     * Renders an admin page with the variables every admin template expects.
     *
     * @param array<string, mixed> $vars
     */
    protected function renderAdmin(string $template, array $vars = []): Response
    {
        return $this->render($template, $vars + [
            'adminMenu' => $this->adminConfig()->getMenu(),
            'dgtxConfiguration' => $this->configuration()->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseVars(AdminContext $context): array
    {
        return [
            'controllerName' => $context->getEntityName(),
            'entityName' => $context->getEntitySlug(),
        ];
    }

    protected function assertGranted(string $permission, AdminContext $context): void
    {
        $this->denyAccessUnlessGranted(
            $permission,
            $context->getEntityConfig(),
            sprintf('Not allowed to %s "%s".', AdminPermission::shortName($permission), $context->getEntityName())
        );
    }

    protected function redirectToList(AdminContext $context): RedirectResponse
    {
        return $this->redirectToRoute('dgtx_admin_entity_read', ['entityName' => $context->getEntitySlug()]);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function trans(string $message, string $domain = self::MESSAGE_DOMAIN, array $parameters = []): string
    {
        return $this->container->get(TranslatorInterface::class)->trans($message, $parameters, $domain);
    }

    protected function adminConfig(): AdminConfig
    {
        return $this->container->get(AdminConfig::class);
    }

    protected function forms(): AdminFormBuilder
    {
        return $this->container->get(AdminFormBuilder::class);
    }

    protected function listView(): ListViewBuilder
    {
        return $this->container->get(ListViewBuilder::class);
    }

    protected function formView(): FormViewBuilder
    {
        return $this->container->get(FormViewBuilder::class);
    }

    protected function templates(): TemplateResolver
    {
        return $this->container->get(TemplateResolver::class);
    }

    protected function persister(): EntityPersister
    {
        return $this->container->get(EntityPersister::class);
    }

    protected function uploads(): UploadHandler
    {
        return $this->container->get(UploadHandler::class);
    }

    protected function configuration(): ConfigurationProvider
    {
        return $this->container->get(ConfigurationProvider::class);
    }

    protected function doctrine(): ManagerRegistry
    {
        return $this->container->get(ManagerRegistry::class);
    }
}
