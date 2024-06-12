<?php

namespace App\Form;

use App\Entity\UserSectionContent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSectionContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', TextType::class, [
                'disabled' => true,
                'label' => 'Usuario',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Descripción',
            ])
            ->add('orderNumber', IntegerType::class, [
                'label' => 'Número de Orden',
                'attr' => [
                    'min' => 0,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSectionContent::class,
        ]);
    }
}
