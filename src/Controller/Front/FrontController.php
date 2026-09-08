<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Form\AdminFormBuilder;
use Digitix\FrameworkBundle\Admin\Persistence\EntityClassResolver;
use Digitix\FrameworkBundle\Entity\Meta;
use Digitix\FrameworkBundle\Entity\Translatable\Translatable;
use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Digitix\FrameworkBundle\Utils\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Base controller for public pages: SEO meta, breadcrumb, configuration
 * values and forms generated from "front_entities".
 *
 * A route declaring "entityName" and "entityId" parameters (e.g. the CMS
 * page) gets its record through getCurrentEntity().
 */
abstract class FrontController extends AbstractController
{
    /** @var array<int, array{name: string, url: string, ico?: string}> */
    public array $breadcrumb = [];

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            ConfigurationProvider::class,
            LanguageProvider::class,
            ManagerRegistry::class,
            EntityClassResolver::class,
            AdminFormBuilder::class,
            AdminConfig::class,
            Mailer::class,
            RequestStack::class,
        ]);
    }

    /**
     * @return array{title: string, description: string, keywords: string, robots: string, canonical: string}
     */
    public function assignMetaVars(): array
    {
        $request = $this->currentRequest();
        $route = (string) $request?->attributes->get('_route');

        $meta = [
            'title' => '',
            'description' => '',
            'keywords' => '',
            'robots' => '',
            'canonical' => '' !== $route
                ? $this->generateUrl($route, (array) $request->attributes->get('_route_params', []), UrlGeneratorInterface::ABSOLUTE_URL)
                : '',
        ];

        $source = $this->getCurrentEntity();

        if (null === $source || null === $this->readMeta($source, 'metaTitle')) {
            $source = '' !== $route
                ? $this->container->get(ManagerRegistry::class)->getRepository(Meta::class)->findOneBy(['page' => $route])
                : null;
        }

        if (null === $source) {
            return $meta;
        }

        $keywords = $this->readMeta($source, 'metaKeywords');
        if (\is_string($keywords) && '' !== $keywords) {
            $decoded = json_decode($keywords, true);
            $keywords = \is_array($decoded)
                ? implode(', ', array_filter(array_map(static fn ($tag) => \is_array($tag) ? ($tag['value'] ?? null) : $tag, $decoded)))
                : $keywords;
        }

        $meta['title'] = (string) $this->readMeta($source, 'metaTitle');
        $meta['description'] = (string) $this->readMeta($source, 'metaDescription');
        $meta['keywords'] = (string) $keywords;

        return $meta;
    }

    /**
     * @return array<int, array{name: string, url: string, ico?: string}>|null
     */
    public function breadcrumb(): ?array
    {
        if (!$this->breadcrumb) {
            return null;
        }

        $home = [
            'name' => $this->trans('Home', [], 'Front.Breadcrumb'),
            'url' => $this->generateUrl('dgtx_index'),
            'ico' => 'home',
        ];

        return array_merge([$home], $this->breadcrumb);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $request = $this->currentRequest();

        return parent::render($view, array_merge([
            'dgtxBreadcrumb' => $this->breadcrumb(),
            'routeName' => str_replace('app_', '', (string) $request?->attributes->get('_route')),
            'meta' => $this->assignMetaVars(),
            'dgtxConfiguration' => $this->container->get(ConfigurationProvider::class)->all(),
        ], $parameters), $response);
    }

    /**
     * Record designated by the "entityName" / "entityId" route parameters, if any.
     */
    protected function getCurrentEntity(): ?object
    {
        $request = $this->currentRequest();
        $name = $request?->attributes->get('entityName');
        $id = $request?->attributes->get('entityId');

        if (!\is_string($name) || null === $id) {
            return null;
        }

        $class = $this->container->get(EntityClassResolver::class)->tryResolve($name);

        return null === $class ? null : $this->container->get(ManagerRegistry::class)->getRepository($class)->find((int) $id);
    }

    /**
     * Form generated from "digitix_framework.front_entities.{$name}.form".
     *
     * @param array<string, mixed> $options
     *
     * @return FormInterface<mixed>
     */
    protected function createFrontForm(string $name, mixed $data = null, array $options = []): FormInterface
    {
        $formConfig = $this->container->get(AdminConfig::class)->getFrontForm($name);
        $builder = $this->container->get(AdminFormBuilder::class);

        $definitions = $builder->buildDefinitions(
            $formConfig,
            new FieldTypeContext($formConfig->translationDomain, null, \is_object($data) ? $data : null)
        );

        return $builder->createFromDefinitions(strtolower($name), $definitions, $data, $options, $this->currentRequest());
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function trans(string $id, array $parameters = [], ?string $domain = null): string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain);
    }

    protected function getConfiguration(string $key): ?string
    {
        return $this->container->get(ConfigurationProvider::class)->getValue($key);
    }

    protected function currentRequest(): ?Request
    {
        return $this->container->get(RequestStack::class)->getCurrentRequest();
    }

    private function readMeta(object $source, string $property): mixed
    {
        $getter = 'get'.ucfirst($property);

        if (method_exists($source, $getter)) {
            return $source->$getter();
        }

        return $source instanceof Translatable ? $source->$property : null;
    }
}
