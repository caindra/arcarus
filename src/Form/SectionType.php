<?php

namespace App\Form;

use App\Entity\Section;
use App\Entity\SectionContent;
use App\Entity\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('height', IntegerType::class, [
                'label' => 'Alto',
                'required' => true,
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('width', IntegerType::class, [
                'label' => 'Ancho',
                'required' => true,
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('maxColQuantity', IntegerType::class, [
                'label' => 'Número máximo de columnas',
                'required' => true,
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('positionTop', IntegerType::class, [
                'label' => 'Posición Arriba',
                'required' => true,
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('positionLeft', IntegerType::class, [
                'label' => 'Posición Izquierda',
                'required' => true,
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('template', EntityType::class, [
                'class' => Template::class,
                'choice_label' => 'styleName',
                'label' => 'Plantilla',
                'required' => true,
                'disabled' => $options['disable_template']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
            'disable_template' => false
        ]);

        $resolver->setAllowedTypes('disable_template', 'bool');
    }
}
