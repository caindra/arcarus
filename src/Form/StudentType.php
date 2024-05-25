<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Student;
use App\Entity\UserSectionContent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('surnames', TextType::class, [
                'label' => 'Apellidos',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('userName', TextType::class, [
                'label' => 'Nombre de usuario',
            ])
            ->add('userSectionContent', EntityType::class, [
                'class' => UserSectionContent::class,
                'choice_label' => 'description',
                'placeholder' => 'Selecciona una opción',
                'label' => 'Sección a la que pertenece el usuario'
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'placeholder' => 'Selecciona una opción',
                'label' => 'Grupo'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
