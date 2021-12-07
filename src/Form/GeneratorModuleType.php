<?php

namespace App\Form;

use App\Entity\GeneratorModule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneratorModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название модуля',
                'attr' => [
                    'placeholder' => 'Название модуля',
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Код модуля',
                'attr' => [
                    'placeholder' => 'Контент модуля',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneratorModule::class,
        ]);
    }
}
