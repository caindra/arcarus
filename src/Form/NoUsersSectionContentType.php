<?php

namespace App\Form;

use App\Entity\SectionContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoUsersSectionContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $group = $options['group'];
        $academicYear = $options['academic_year'];
        $organization = $options['organization'];

        $builder
            ->add('options', ChoiceType::class, [
                'choices' => [
                    'Nombre del Grupo: ' . $group->getName() => 'group_name',
                    'Descripción del Año Académico: ' . $academicYear->getDescription() => 'academic_year_description',
                    'Nombre de la Organización: ' . $organization->getName() => 'organization_name',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => 'Seleccione las opciones para la sección',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Guardar',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);

        $resolver->setRequired(['group', 'academic_year', 'organization']);
    }
}
