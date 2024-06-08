<?php

namespace App\Form;

use App\Entity\Organization;
use App\Entity\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('styleName', TextType::class, [
                'label' => 'Nombre del estilo'
            ])
            ->add('layout', FileType::class, [
                'label' => 'Subir imagen',
                'mapped' => false,
                'required' => false
            ])
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'placeholder' => 'Selecciona una opción',
                'label' => 'Centro educativo'
            ])
            ->add('sections', CollectionType::class, [
                'entry_type' => SectionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Template::class,
        ]);
    }
}
