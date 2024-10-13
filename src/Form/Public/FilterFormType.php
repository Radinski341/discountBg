<?php

namespace App\Form\Public;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productsPerPage', ChoiceType::class, [
                'choices'=>[
                    '24' => 24,
                    '48' => 48,
                    '60' => 60
                ],
                'label' => 'Продукти на страница:'
            ])
            ->add('order', ChoiceType::class, [
                'choices' => [
                    'Най нови' => 'new',
                    'Цена възх.' => 'priceAsc',
                    'Цена низх.' => 'priceDesc',
                    'Отстъпка' => 'discount'
                ],
                'label' => 'Подреди по:'
            ])
            ->add('priceRangeFrom', RangeType::class, [
                'label' => 'Цена От - До:'
            ])
            ->add('priceRangeTo', RangeType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Търси',
                'attr' => [
                    'class' => 'btn btn-success'
                ]

            ]);

    }


}