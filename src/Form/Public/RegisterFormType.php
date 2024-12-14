<?php

namespace App\Form\Public;

use App\Entity\User;
use App\Service\CityService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFormType extends AbstractType
{
    private CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Е-майл',
                'attr' => [
                    'placeholder' => 'example@example.com'
                ]

            ])
            ->add('fullName', TextType::class, [
                'label' => 'Цяло име',
                'attr' => [
                    'placeholder' => 'Иван Иванов'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Парола',
                'attr' => [
                    'placeholder' => '********'
                ]
            ])
            ->add('repeatPassword', PasswordType::class, [
                'label' => 'Повтори паролата',
                'mapped' => false,
                'attr' => [
                    'placeholder' => '********'
                ]
            ])
            ->add('city', ChoiceType::class, [
                'choices' => $this->cityService->getAllCities(),
                'placeholder' => '---Избери Град---',
                'required' => true,
                'label' => 'Град',
                'attr' => [
                    'data-city-target' => 'cityField'
                ]
            ])
            ->add('populatedPlace', TextType::class, [
                'required' => false,
                'label' => 'Населено място',
                'attr' => [
                    'placeholder' => 'Празно поле получава стойност избрания град'
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Телефонен номер',
                'attr' => [
                    'placeholder' => '+359 888 123 456 / 0888 123 456'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Адрес',
                'attr' => [
                    'placeholder' => 'ул. Цветна 5, ап. 10, София, 1000'
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Регистрирайте се'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration']
        ]);
    }
}