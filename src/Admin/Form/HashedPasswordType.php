<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Password input that hashes the submitted value straight into the "user"
 * option (the entity being edited) while leaving the plain value on the
 * mapped property (typically "plainPassword"). An empty submit keeps the
 * current password.
 */
final class HashedPasswordType extends AbstractType
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder->addModelTransformer(new CallbackTransformer(
            static fn (): ?string => null,
            function (mixed $plain) use ($user): ?string {
                if (null === $plain || '' === $plain) {
                    return null;
                }

                if (!\is_string($plain)) {
                    throw new TransformationFailedException(sprintf('Expected a string, got "%s".', get_debug_type($plain)));
                }

                if ($user instanceof PasswordAuthenticatedUserInterface && method_exists($user, 'setPassword')) {
                    $user->setPassword($this->hasher->hashPassword($user, $plain));
                }

                return $plain;
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['always_empty'] || !$form->isSubmitted()) {
            $view->vars['value'] = '';
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => null,
            'always_empty' => true,
            'trim' => false,
        ]);

        $resolver->setAllowedTypes('user', ['null', 'object']);
    }

    public function getParent(): string
    {
        return PasswordType::class;
    }
}
