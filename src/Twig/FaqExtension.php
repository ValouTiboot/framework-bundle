<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Twig;

use Digitix\FrameworkBundle\Entity\Faq;
use Digitix\FrameworkBundle\Repository\FaqRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Front access to the FAQ, the markup being left to the project:
 *
 *   {% for faq in dgtx_faq() %}{{ faq.question }} {{ faq.answer|raw }}{% endfor %}
 *   {% set faqs = dgtx_faq(4) %}   the first four only
 */
final class FaqExtension extends AbstractExtension
{
    public function __construct(private readonly FaqRepository $faqs)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dgtx_faq', $this->items(...)),
        ];
    }

    /** @return list<Faq> */
    public function items(?int $limit = null): array
    {
        return $this->faqs->findActive($limit);
    }
}
