<?php

namespace App\Form\Public;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductInfoFormType extends AbstractType
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $options['data']['optionsArray'];
        if($choices){
            foreach ( $options['data']['optionsArray'] as $optionName => $optionValues) {
                $optionSluggedName = $this->slugger->slug($optionName)->toString();

                $builder->add('choices', ChoiceType::class, [
                    'choices' => $optionValues,
                    'label' => $optionName,
                    'attr' => [
                        'onchange' => 'location = this.value',
                        'class' => $optionSluggedName,
                    ]
                ]);
            }
        }
        $builder->add('quantity', TextType::class, [
            'disabled' => true,
            'label' => "Количество",
            'label_attr' => [
                'class' => 'mb-2 d-block'
            ],
            'attr' => [
                'class' => 'form-control text-center border border-secondary quantity-value'
            ]
        ]);

    }
}
//<input type="text" class="form-control text-center border border-secondary quantity-value" disabled />