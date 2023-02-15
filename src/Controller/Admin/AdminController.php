<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Digitix\FrameworkBundle\Controller\Controller;
use Digitix\FrameworkBundle\Factory\DigitixParameterFactory;
use Digitix\FrameworkBundle\Factory\FieldFactory;
use Digitix\FrameworkBundle\Factory\HelperFormFactory;
use Digitix\FrameworkBundle\Factory\HelperListFactory;
use Digitix\FrameworkBundle\Factory\HelperViewFactory;

class AdminController extends Controller
{
    /**
     * Description
     * @param type $attributes
     * @param type|null $subject
     * @param string|string $message
     * @return void|Execption
     */
    public function checkAccess($attributes, $subject = null, string $message = 'Access Denied toto.')
    {
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
        ] + parent::getSubscribedServices();
    }

    /**
     *
     * @return Response
     */
    public function view()
    {
        $helperView = $this->get('dgtx.helper.view.factory')->build([]);

        return $this->display($helperView->generateView());
    }

    /**
     *
     * @return Response
     */
    public function read()
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
    public function create()
    {

        dump($this->get('dgtx.parameter.factory')->build());
        $tplVars = [];
        $form = $this->get('dgtx.form.factory')->buildForm();
        $helperForm = $this->get('dgtx.helper.form.factory')->build($form, $tplVars);

        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * TODO Validator
             * TODO uplaod files
             */

            $this->persistEntity();
            $this->addFlash('success', $this->getContext()->trans('Entity successfuly added.', [], 'Admin.Message.Success'));

            return $this->redirectToRoute(
                'dgtx_admin_entity_read',
                ['entityName' => $this->getContext()->getEntityName()]
            );
        }

        return $this->display($helperForm->generateForm());
    }

    /**
     * @return Response
     */
    public function edit()
    {
        dump($this->get('dgtx.parameter.factory')->build()->getParameters());
        $tplVars = [];
        $form = $this->get('dgtx.form.factory')->buildForm();
        $helperForm = $this->get('dgtx.helper.form.factory')->build($form, $tplVars);

        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * TODO Validator
             * TODO uplaod files
             */

            $this->persistEntity();
            $this->addFlash('success', $this->getContext()->trans('Entity successfuly updated.', [], 'Admin.Message.Success'));

            return $this->redirectToRoute(
                'dgtx_admin_entity_read',
                ['entityName' => $this->getContext()->getEntityName()]
            );
        }

        return $this->display($helperForm->generateForm());
    }

    /**
     *
     * @return Redirect
     */
    public function delete()
    {
        $entity = $this->getContext()->getEntity()->getInstance();

        if ($entity === null) {
            $this->addFlash('info', $this->getContext()->trans('This entity does not exist anymore.', [], 'Admin.Message.Info'));
        } else {
            $this->get('dgtx.entity.manager')->removeEntity($entity);
            $this->addFlash('success', $this->getContext()->trans('Entity successfuly deleted.', [], 'Admin.Message.Success'));
        }

        return $this->redirectToRoute(
            'dgtx_admin_entity_read',
            ['entityName' => $this->getContext()->getEntityName()]
        );
    }

    /**
     * @return JsonResponse
     */
    public function ajaxSortable()
    {
        $entityName = $this->getContext()->getEntityName();
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

