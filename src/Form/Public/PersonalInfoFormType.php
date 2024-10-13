<?php

namespace App\Form\Public;

use App\Service\CityService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonalInfoFormType extends AbstractType
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
                'disabled' => true,
            ])
            ->add('fullName', TextType::class, [
                'label' => 'Цяло име',
            ])
            ->add('city', ChoiceType::class, [
                'label' => 'Град',
                'choices' => $this->cityService->getAllCities()
            ])
            ->add('populatedPlace', TextType::class, [
                'label' => 'Населено място',
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Телефонен номер',
            ])
            ->add('address', TextType::class, [
                'label' => 'Адрес',
            ])
            //Add a plain password field with a dumb value, so it doesn't make problems on submitting
            ->add('plainPassword', HiddenType::class, [
                'required' => false,
                'attr' => [
                    'value' => '@uiVJY%7UF6RV5^yhv&KU21-BNiif12FEW@'
                ]
            ]);
    }

}