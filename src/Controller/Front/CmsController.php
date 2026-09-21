<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Front;

use Digitix\FrameworkBundle\Entity\Cms;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public CMS page, addressed by its rewrite alone ("/mentions-legales.html").
 *
 * The route is matched last (negative priority) so that the project's own
 * "/something.html" routes always win. Once the page is found, its id is put
 * in the "entityId" request attribute: FrontController then reads the SEO
 * meta from the record like for any entity page.
 */
class CmsController extends FrontController
{
    #[Route('/{rewrite}.html', name: 'front_cms_show', defaults: ['entityName' => 'cms'], requirements: ['rewrite' => '[a-zA-Z0-9_-]+'], methods: ['GET'], priority: -10)]
    public function show(string $rewrite): Response
    {
        $cms = $this->findByRewrite($rewrite);

        if (null === $cms) {
            throw $this->createNotFoundException(sprintf('No CMS page with rewrite "%s".', $rewrite));
        }

        // a rewrite of another language: send to the one of the current language
        if ($cms->getRewrite() !== $rewrite) {
            return $this->redirectToRoute('front_cms_show', ['rewrite' => $cms->getRewrite()], 301);
        }

        $this->currentRequest()?->attributes->set('entityId', $cms->getId());

        $this->breadcrumb[] = [
            'name' => (string) $cms->getName(),
            'url' => $this->generateUrl('front_cms_show', ['rewrite' => $rewrite]),
        ];

        // the project template when it exists, otherwise the bundle default
        $template = $this->container->get('twig')->getLoader()->exists('cms/show.html.twig')
            ? 'cms/show.html.twig'
            : '@DigitixFramework/front/cms/show.html.twig';

        return $this->render($template, ['cms' => $cms]);
    }

    /**
     * Former URL scheme ("/12-about-us.html"): permanent redirect to the page.
     */
    #[Route('/{entityId}-{rewrite}.html', name: 'front_cms_show_legacy', requirements: ['entityId' => '\d+', 'rewrite' => '[a-zA-Z0-9_-]+'], methods: ['GET'], priority: -5)]
    public function legacy(int $entityId): Response
    {
        $cms = $this->container->get(ManagerRegistry::class)->getRepository(Cms::class)->find($entityId);

        if (!$cms instanceof Cms || !$cms->getActive() || null === $cms->getRewrite()) {
            throw $this->createNotFoundException(sprintf('No CMS page with id %d.', $entityId));
        }

        return $this->redirectToRoute('front_cms_show', ['rewrite' => $cms->getRewrite()], 301);
    }

    /**
     * Active page having this rewrite in any language; the one of the current
     * language first when several match.
     */
    private function findByRewrite(string $rewrite): ?Cms
    {
        $repository = $this->container->get(ManagerRegistry::class)->getRepository(Cms::class);

        if (!$repository instanceof EntityRepository) {
            return null;
        }

        /** @var Cms[] $pages */
        $pages = $repository->createQueryBuilder('c')
            ->join('c.translations', 't')
            ->where('c.active = true')
            ->andWhere('t.rewrite = :rewrite')
            ->setParameter('rewrite', $rewrite)
            ->getQuery()
            ->getResult();

        foreach ($pages as $page) {
            if ($page->getRewrite() === $rewrite) {
                return $page;
            }
        }

        return $pages[0] ?? null;
    }
}
