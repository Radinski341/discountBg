<?php

namespace App\Form\Public;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends  AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Сегашна парола',
                'constraints' => new NotBlank(['message' => "Моля въведете парола"]),
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Нова парола',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 8, 'minMessage' => 'Паролата мора да е минимум 8 карактера']),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Потвръди паролата',
                'constraints' => new NotBlank(['message' => "Моля въведете парола"]),
            ]);
    }
}