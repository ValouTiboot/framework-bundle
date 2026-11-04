<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Utils;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

/**
 * Bot detection for the front forms, without any third party:
 *
 * - a honeypot: a text field hidden from humans (off-screen, not tabbable,
 *   aria-hidden) that bots fill in because it looks like any other field;
 * - a minimum fill time: a hidden field carries the time the form was
 *   rendered, signed with the application secret so it cannot be forged.
 *
 * Both fields are unmapped: they never reach the form data.
 */
final class SpamGuard
{
    public const HONEYPOT_FIELD = 'website';
    public const TIMESTAMP_FIELD = 'renderedAt';
    /** A human needs at least this long between the display of the form and its submission. */
    public const MIN_SECONDS = 3;

    public function __construct(private readonly string $secret)
    {
    }

    /**
     * Child definitions to append to a front form (same shape as AdminFormBuilder::buildDefinitions()).
     *
     * @return list<array{name: string, type: string, options: array<string, mixed>}>
     */
    public function definitions(?int $now = null): array
    {
        return [
            [
                'name' => self::HONEYPOT_FIELD,
                'type' => TextType::class,
                'options' => [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Leave this field empty',
                    'translation_domain' => 'Front.Form',
                    'attr' => ['autocomplete' => 'off', 'tabindex' => '-1'],
                    'row_attr' => [
                        'class' => 'dgtx-honeypot',
                        'style' => 'position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;',
                        'aria-hidden' => 'true',
                    ],
                ],
            ],
            [
                'name' => self::TIMESTAMP_FIELD,
                'type' => HiddenType::class,
                'options' => [
                    'mapped' => false,
                    'data' => $this->sign($now ?? time()),
                ],
            ],
        ];
    }

    /** Value of the timestamp field for a form rendered at $time. */
    public function sign(int $time): string
    {
        return $time.'.'.hash_hmac('sha256', (string) $time, $this->secret);
    }

    /**
     * True when the submitted form was filled by a bot: honeypot filled,
     * timestamp missing, forged or too recent. Forms without the guard
     * fields are never spam.
     *
     * @param FormInterface<mixed> $form
     */
    public function isSpam(FormInterface $form, ?int $now = null): bool
    {
        if (!$form->has(self::HONEYPOT_FIELD) || !$form->has(self::TIMESTAMP_FIELD)) {
            return false;
        }

        $honeypot = $form->get(self::HONEYPOT_FIELD)->getData();
        if (null !== $honeypot && '' !== $honeypot) {
            return true;
        }

        $stamp = $form->get(self::TIMESTAMP_FIELD)->getData();
        if (!\is_string($stamp) || !str_contains($stamp, '.')) {
            return true;
        }

        [$time, $signature] = explode('.', $stamp, 2);

        if (!ctype_digit($time) || !hash_equals(hash_hmac('sha256', $time, $this->secret), $signature)) {
            return true;
        }

        return ($now ?? time()) - (int) $time < self::MIN_SECONDS;
    }
}
