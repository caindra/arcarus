<?php

namespace App\Form;

use App\Entity\AcademicYear;
use App\Entity\ClassPicture;
use App\Entity\Group;
use App\Entity\Organization;
use App\Entity\Professor;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre del grupo',
            ])
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'name',
                'label' => 'Organización',
            ])
            ->add('academicYear', EntityType::class, [
                'class' => AcademicYear::class,
                'choice_label' => 'description',
                'label' => 'Año académico',
            ])
            ->add('professors', EntityType::class, [
                'class' => Professor::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Profesores',
            ])
            ->add('students', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Estudiantes',
            ])
            ->add('mentors', EntityType::class, [
                'class' => Professor::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Tutor/a',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
