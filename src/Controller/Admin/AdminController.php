<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Digitix\FrameworkBundle\Controller\Controller;
use Digitix\FrameworkBundle\Factory\HelperListFactory;
use Digitix\FrameworkBundle\Factory\HelperViewFactory;
// use Symfony\Component\Routing\Annotation\Route;

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
            'dgtx.helper.view.factory' => '?'. HelperViewFactory::class,
            'dgtx.helper.list.factory' => '?'. HelperListFactory::class,
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

        // $fieldConfig = $this->get('dgtx.entity.config');
        // $listFields = $fieldConfig->getListFields();
        // $sorter = $this->get('dgtx.sorter.factory')->build($listFields);

        // $filters = $this->get('dgtx.filter.factory')->build();
        // $filterForm = $this->get('dgtx.form.factory')->buildFormFilters($filters);
        // $search = $this->get('dgtx.search.factory')->build($filters, $filterForm);

        // $dql = $this->get('dgtx.entity.repository')->buildQuery($listFields, $search, $sorter);


        // $paginator = $this->get('dgtx.paginator.factory')->build($dql)->paginate();

        $helperList = $this->get('dgtx.helper.list.factory')->build($tplVars);

        return $this->display($helperList->generateList());
    }

    /**
     * view for form creation Entity
     *
     * @param string $entityName
     * @return Response
     */
    public function create(string $entityName)
    {
        $tplVars = [];

        $fieldConfig = $this->get('dgtx.field.config');
        $fields = $this->get('dgtx.field.factory')->build($fieldConfig);
        $form = $this->get('dgtx.form.factory')->buildForm($fields);
        $helperForm = $this->get('dgtx.helper.form.factory')->build($fieldConfig, $form, $tplVars);
        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->persistEntity();
            // process uploadFiles
            $this->addFlash('success', $this->getContext()->trans('Entity successfuly added.', [], 'Admin.Message.Success'));
            return $this->redirectToRoute('dgtx_admin_entity_read', ['entityName' => $entityName]);
        }

        return $this->display($helperForm->generateForm());
    }

    /**
     * @return Response
     */
    public function edit(string $entityName)
    {
        $tplVars = [];

        $fieldConfig = $this->get('dgtx.field.config');
        $fields = $this->get('dgtx.field.factory')->build($fieldConfig);
        $form = $this->get('dgtx.form.factory')->buildForm($fields);
        $helperForm = $this->get('dgtx.helper.form.factory')->build($fieldConfig, $form, $tplVars);
        $errors = $form->getErrors(true, false);

        if (count($errors) > 0) {
            $this->addFlash('danger', $errors);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // mettre le validator dans le factory pour le retrouver dans le context
            $this->persistEntity();
            // process uploadFiles dans le persisterAfter
            $this->addFlash('success', $this->getContext()->trans('Entity successfuly updated.', [], 'Admin.Message.Success'));
            return $this->redirectToRoute('dgtx_admin_entity_read', ['entityName' => $entityName]);
        }

        return $this->display($helperForm->generateForm());
    }

    /**
     *
     * @return Redirect
     */
    public function delete(string $entityName, int $entityId)
    {
        $entity = $this->get('dgtx.entity.repository')->find($entityId);

        if (is_null($entity)) {
            $this->addFlash('info', $this->getContext()->trans('This entity does not exist anymore.', [], 'Admin.Message.Info'));
        } else {
            $this->addFlash('success', $this->getContext()->trans('Entity successfuly deleted.', [], 'Admin.Message.Success'));
        }

        /*
         *   TODO delete images if have some
        */

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();

        return $this->redirectToRoute('dgtx_admin_entity_read', ['entityName' => $entityName]);
    }

    /**
     * @return JsonResponse
     */
    public function ajaxSortable($entityName)
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

        $this->get('dgtx.entity.persister')->persistObjects($items);

        return $this->displayAjax(['success' => true]);
    }

    public function assignMetaVars()
    {
        return;
    }
}

