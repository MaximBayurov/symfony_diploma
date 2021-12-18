<?php

namespace App\Form;

use App\Entity\Article;
use Maxim\ArticleThemesBundle\BaseArticleThemesProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ArticleCreateType extends AbstractType
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private BaseArticleThemesProvider $themesProvider,
    ) {
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**  @var Article| null $article */
        $article = $options['data'] ?? null;
        
        $builder
            ->add('theme', ChoiceType::class, [
                'choices' => $this->themesProvider->getThemesList(),
                'label' => 'Тематика',
                'invalid_message' => 'Некорректно указана тема'
            ])
            ->add('title', TextType::class, $this->setDefaultOptions('Заголовок статьи'))
            ->add('keyword', CollectionType::class, [
                'entry_options' => [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                ],
            ])
            ->add('sizeFrom', NumberType::class, $this->setDefaultOptions('Размер статьи от'))
            ->add('sizeTo', NumberType::class, $this->setDefaultOptions('До'))
            ->add('words', CollectionType::class, [
                'entry_type' => CollectionType::class,
                'entry_options' => [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'allow_add' => true,
                ],
                'allow_add' => true,
                'prototype' => true,
                'required' => false,
            ]);
        
        if($article
            && $article->getAuthor()
            && $this->authorizationChecker->isGranted('SUBSCRIPTION_PLUS')) {
            $builder
                ->add('images', FileType::class, [
                    'label' => 'Изображения',
                    'multiple' => 'multiple',
                    'attr' => [
                        'accept' => 'image/*',
                        'multiple' => 'multiple'
                    ],
                    'required' => false
            ]);
        }
        
        $builder->add('submit', SubmitType::class);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
    
    private function setDefaultOptions(string $fieldName): array
    {
        return [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => $fieldName,
            ],
            'label' => $fieldName,
            'required' => false,
            'invalid_message' => 'Некорректно указано поле - ' . mb_strtolower($fieldName)
        ];
    }
}
