<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, $this->setDefaultOptions(
                "Ваше имя"
            ))
            ->add('email', EmailType::class,  $this->setDefaultOptions(
                "Ваш Email"
            ))
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => $this->setDefaultOptions('Пароль'),
                'second_options' => $this->setDefaultOptions('Подтверждение пароля'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста, введите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Слишком короткий пароль. Должен быть не менее 6 символов',
                    ]),
                ],
                'invalid_message' => "Пароли должны совпадать"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
    
    private function setDefaultOptions(string $label): array
    {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $label,
                'class' => 'form-control'
            ],
            'row_attr' => [
                'class' => 'form-label-group'
            ],
            'error_mapping' => false,
            'required' => true,
        ];
    }
}
