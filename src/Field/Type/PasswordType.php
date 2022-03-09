<?php

namespace Digitix\FrameworkBundle\Field\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordType extends AbstractType
{
	private $passwordHasher;
	private $contextProvider;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ContextProvider $contextProvider)
    {
        $this->passwordHasher = $passwordHasher;
        $this->context = $contextProvider->getContext();
    }

	public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    	$hasher = $this->passwordHasher;
    	$entityInstance = $this->context->getEntity()->getInstance();

        $builder->addModelTransformer(
        	new CallbackTransformer(
                function (){ return null; },
                function ($value) use ($hasher, $entityInstance): ?string {
                    if ($value === null) {
                        return null;
                    }

                    if (!is_string($value)) {
                        throw new TransformationFailedException('Expected string got "' . gettype($value) . '"');
                    }

                    $entityInstance->setPassword($hasher->hashPassword($entityInstance, $value));

                    return $value;
                }
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['always_empty'] || !$form->isSubmitted()) {
            $view->vars['value'] = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'always_empty' => true,
            'trim' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'password';
    }
}