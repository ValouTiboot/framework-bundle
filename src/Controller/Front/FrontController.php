<?php

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Controller\Controller;
use Digitix\FrameworkBundle\Config\FrontFormConfigInterface;
use Digitix\FrameworkBundle\Entity\Meta;
use Digitix\FrameworkBundle\Factory\FieldFactory;
use Digitix\FrameworkBundle\Factory\FormFactory;
use Digitix\FrameworkBundle\Factory\EntityFactory;
use Digitix\FrameworkBundle\Provider\EntityRepositoryProvider;
use Digitix\FrameworkBundle\Orm\EntityPersister;
use Digitix\FrameworkBundle\Repository\EntityRepository;
use Digitix\FrameworkBundle\Utils\Mail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontController extends Controller
{
    public $breadcrumb = [];

	public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            'password.encoder' => '?'. UserPasswordEncoderInterface::class,
            'dgtx.filter.factory' => '?'.FilterFactory::class,
            'dgtx.field.factory' => '?'.FieldFactory::class,
            'dgtx.paginator.factory' => '?'.PaginatorFactory::class,
            'dgtx.helper.list.factory' => '?'.HelperListFactory::class,
            'dgtx.helper.form.factory' => '?'.HelperFormFactory::class,
            'dgtx.helper.view.factory' => '?'.HelperViewFactory::class,
            'dgtx.entity.repository' => '?'.EntityRepository::class,
            'dgtx.entity.repository.provider' => '?'.EntityRepositoryProvider::class,
            'dgtx.entity.persister' => '?'.EntityPersister::class,
            'dgtx.entity.factory' => '?'.EntityFactory::class,
            'dgtx.entity.config' => '?'.EntityConfigInterface::class,
            'dgtx.front.form.config' => '?'.FrontFormConfigInterface::class,
            'dgtx.view.config' => '?'.ViewConfigInterface::class,
            'dgtx.sorter' => '?'.SorterFactory::class,
            'dgtx.search' => '?'.SearchFactory::class,
            'dgtx.form.factory' => '?'.FormFactory::class,
            'dgtx.mailer' => '?'. Mail::class,
            'knp.pdf' => '?'. Pdf::class,
        ]);
    }

    public function assignMetaVars()
    {
        if (null !== $this->getContext()->getEntity()
            && is_object($this->getContext()->getEntity()->getInstance())
            && (
                method_exists($this->getContext()->getEntity()->getInstance(), 'getMetaTitle')
                || (class_exists($this->getContext()->getEntity()->getFqcn().'Translation') && method_exists($this->getContext()->getEntity()->getFqcn().'Translation', 'getMetaTitle'))
            )
        )
            $currentMeta = $this->getContext()->getEntity()->getInstance();
        else
        {
            $metaRepository = $this->get('dgtx.entity.repository.provider')->getRepository(Meta::class);
            $currentMeta = $metaRepository->findOneBy(['page' => $this->getContext()->getRequest()->get('_route')]);
        }

        $routeParams = $this->getContext()->getRequest()->get('_route_params');
        $canonical = $this->generateUrl(
            $this->getContext()->getRequest()->get('_route'),
            $routeParams,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $meta = [
            'title' => '',
            'description' => '',
            'keywords' => '',
            'robots' => '',
            'canonical' => $canonical,
        ];

        if ($currentMeta !== null)
        {
            $_keywords = $currentMeta->getMetaKeywords();
            if ($_keywords !== null)
            {
                $_arr = [];
                $keywords = json_decode($_keywords);
                if (count($keywords))
                foreach ($keywords as $value)
                    $_arr[] = $value->value;

                $_keywords = implode(', ', $_arr);
            }

            // TODO : route params rewrite

            $meta = [
                'title' => $currentMeta->getMetaTitle(),
                'description' => $currentMeta->getMetaDescription(),
                'keywords' => $_keywords,
                'robots' => '',
                'canonical' => $canonical,
            ];
        }

        return $meta;
    }

    public function breadcrumb()
    {
        if (!count($this->breadcrumb))
            return null;

        $breadcrumb = [];
        $breadcrumb[] = [
            'name' => $this->getContext()->trans('Home', [], 'Front.Breadcrumb'),
            'url' => $this->generateUrl('dgtx_index', [], 0),
            'ico' => 'home'
        ];

        return array_merge($breadcrumb, $this->breadcrumb);
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        return parent::render($view, array_merge(['dgtxBreadcrumb' => $this->breadcrumb()], $parameters), $response);
    }
}