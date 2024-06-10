<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangeUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['admin']) {
            $builder->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Contraseña actual',
                'constraints' => [
                    new NotBlank(),
                    new UserPassword([
                        'message' => 'Contraseña actual incorrecta']
                    ),
                ],
            ]);
        }
        $builder
            ->add('newPassword', RepeatedType::class, [
                'label' => 'Nueva contraseña',
                'required' => true,

                'type' => PasswordType::class,
                'mapped' => false,

                'invalid_message' => 'No coinciden las contraseñas',
                'first_options' => [
                    'label' => 'Nueva contraseña',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor, introduce una nueva contraseña.',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'La nueva contraseña debe tener al menos {{ limit }} caracteres.',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'La nueva contraseña debe contener al menos una letra minúscula.',
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'La nueva contraseña debe contener al menos una letra mayúscula.',
                        ]),
                        new Regex([
                            'pattern' => '/[0-9]/',
                            'message' => 'La nueva contraseña debe contener al menos un número.',
                        ]),
                        new Regex([
                            'pattern' => '/[\W]/',
                            'message' => 'La nueva contraseña debe contener al menos un carácter especial.',
                        ]),
                    ]
                ],
                'second_options' => [
                    'label' => 'Repite la nueva contraseña',
                    'required' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'admin' => false,
        ]);
    }
}
