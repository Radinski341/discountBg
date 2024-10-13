<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class DataUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('file', FileType::class, [
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '50M'
                            ])
                        ]
                    ])
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ]
            ])
            ->add('upload', SubmitType::class);
    }
}