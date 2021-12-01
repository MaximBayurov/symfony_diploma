<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateDemoArticleFormType extends BaseFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->setDefaultOptions("Заголовок статьи"))
            ->add('words', TextType::class, $this->setDefaultOptions("Продвигаемое слово"))
            ->add('submit', SubmitType::class, [
                'label' => 'Попробовать',
                'attr' => [
                    'class' => 'btn btn-lg btn-primary btn-block text-uppercase'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model\DemoArticle::class,
        ]);
    }
}
