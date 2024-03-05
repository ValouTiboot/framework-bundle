<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Digitix\FrameworkBundle\Factory\FieldFactory;
use Digitix\FrameworkBundle\Controller\Controller;
use Digitix\FrameworkBundle\Factory\HelperFormFactory;
use Digitix\FrameworkBundle\Factory\HelperListFactory;
use Digitix\FrameworkBundle\Factory\HelperViewFactory;
use Digitix\FrameworkBundle\Factory\DigitixParameterFactory;
use Digitix\FrameworkBundle\Provider\EntityRepositoryProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends Controller
{
    /**
     * Description
     * @param type $attributes
     * @param type|null $subject
     * @param string|string $message
     * @return void|Execption
     */
    public function checkAccess(
        $attributes,
        $subject = null,
        string $message = 'Access Denied toto.'
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    	$this->denyAccessUnlessGranted($attributes, $subject, $message);
    }

    public static function getSubscribedServices(): array
    {
        return [
            'dgtx.helper.view.factory' => '?'.HelperViewFactory::class,
            'dgtx.helper.list.factory' => '?'.HelperListFactory::class,
            'dgtx.helper.form.factory' => '?'.HelperFormFactory::class,
            'dgtx.field.factory' => '?'.FieldFactory::class,
            'dgtx.parameter.factory' => '?'.DigitixParameterFactory::class,
            'dgtx.entity.repository.provider' => '?'.EntityRepositoryProvider::class,
        ] + parent::getSubscribedServices();
    }

    /**
     *
     * @return Response
     */
    public function view(string $entityName)
    {
        $helperView = $this->get('dgtx.helper.view.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                []
            )
        ;

        return $this->display($helperView->generateView());
    }

    /**
     *
     * @return Response
     */
    public function viewEntity(string $entityName, int $entityId)
    {
        $helperView = $this->get('dgtx.helper.view.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                []
            )
        ;

        return $this->display($helperView->generateView());
    }

    /**
     *
     * @return Response
     */
    public function read(string $entityName)
    {
        $tplVars = [];
        $helperList = $this->get('dgtx.helper.list.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $tplVars
            )
        ;

        return $this->display($helperList->generateList());
    }

    /**
     * view for form creation Entity
     *
     * @return Response
     */
    public function create(string $entityName)
    {
        $tplVars = [];
        $form = $this->get('dgtx.form.factory')
            ->buildForm(
                [
                    'validation_groups' => ['Default','Create']
                ]
            )
        ;

        $helperForm = $this->get('dgtx.helper.form.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $form,
                $tplVars
            )
        ;

        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (true === $helperForm->hasUploadFields()) {
                $filesName = $helperForm->getUploadFields();
                $entity = $this->getContext()->getEntity()->getInstance();

                foreach ($filesName as $fileName) {
                    $file = $form->get($fileName)->getData();

                    if (null === $file) {
                        continue;
                    }

                    $fileUplaodDir = $this->createFileUploadDir(
                        lcfirst(
                            $this->getContext()->getEntityName()
                        ).'/'.$fileName.'/'
                    );

                    // FileName
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $slug = $this->get('slugger')->slug($originalFilename);
                    $newFileName = $slug.'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move($fileUplaodDir,$newFileName);
                        $entity->{'set'.ucFirst($fileName)}($newFileName);
                    } catch (FileException $exception) {
                        $errors[] = 'File Exception : '.$exception->getMessage();
                    }
                }
            }

            $this->persistEntity();
            $this->addFlash(
                'success',
                $this->getContext()->trans('Entity successfuly added.', [], 'Admin.Message.Success')
            );

            return $this->redirectToRoute(
                'dgtx_admin_entity_read',
                ['entityName' => $entityName]
            );
        }

        return $this->display($helperForm->generateForm());
    }

    /**
     * @return Response
     */
    public function edit(string $entityName, int $entityId)
    {
        $tplVars = [];
        $form = $this->get('dgtx.form.factory')->buildForm();
        $helperForm = $this->get('dgtx.helper.form.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $form,
                $tplVars
            )
        ;

        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (true === $helperForm->hasUploadFields()) {
                $filesName = $helperForm->getUploadFields();
                $entity = $this->getContext()->getEntity()->getInstance();

                foreach ($filesName as $fileName) {
                    $file = $form->get($fileName)->getData();

                    if (null === $file) {
                        continue;
                    }

                    $fileUplaodDir = $this->createFileUploadDir(
                        lcfirst(
                            $this->getContext()->getEntityName()
                        ).'/'.$fileName.'/'
                    );

                    // FileName
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $slug = $this->get('slugger')->slug($originalFilename);
                    $newFileName = $slug.'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move($fileUplaodDir,$newFileName);
                        $entity->{'set'.ucFirst($fileName)}($newFileName);
                    } catch (FileException $exception) {
                        $errors[] = 'File Exception : '.$exception->getMessage();
                    }
                }
            }

            $this->persistEntity();
            $this->addFlash(
                'success',
                $this->getContext()->trans('Entity successfuly updated.', [], 'Admin.Message.Success')
            );

            return $this->redirectToRoute(
                'dgtx_admin_entity_read',
                ['entityName' => $entityName]
            );
        }

        return $this->display($helperForm->generateForm());
    }

    /**
     *
     * @return Redirect
     */
    public function delete(string $entityName, int $entityId)
    {
        $entity = $this->getContext()->getEntity()->getInstance();

        if ($entity === null) {
            $this->addFlash(
                'info',
                $this->getContext()->trans('This entity does not exist anymore.', [], 'Admin.Message.Info')
            );
        } else {
            $this->get('dgtx.entity.manager')->removeEntity($entity);
            $this->addFlash(
                'success',
                $this->getContext()->trans('Entity successfuly deleted.', [], 'Admin.Message.Success')
            );
        }

        return $this->redirectToRoute(
            'dgtx_admin_entity_read',
            ['entityName' => $entityName]
        );
    }

    /**
     * @return JsonResponse
     */
    public function ajaxSortable(string $entityName)
    {
        $params = $this->getContext()->getRequest()->request->all();

        if (!isset($params[$entityName])) {
            return $this->displayAjax(['success' => false]);
        }

        $items = new ArrayCollection();
        foreach ($params[$entityName] as $key => $itemId) {
            $item = $this->get('dgtx.entity.repository')->findOneBy(['id' => $itemId]);

            if ($item === null) {
                continue;
            }

            $item->setPosition($key+1);
            $items->add($item);
        }

        $this->get('dgtx.entity.manager')->persistObjects($items);

        return $this->displayAjax(['success' => true]);
    }

    public function assignMetaVars()
    {
        return;
    }
}
