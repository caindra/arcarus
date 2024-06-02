<?php

namespace App\Form;

use App\Entity\ClassPicture;
use App\Entity\Section;
use App\Entity\SectionContent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título',
            ])
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'label' => 'Sección',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('classPicture', EntityType::class, [
                'class' => ClassPicture::class,
                'label' => 'Orla',
                'multiple' => false,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SectionContent::class,
        ]);
    }
}
