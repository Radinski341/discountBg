<?php

namespace App\Form\Public;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autocomplete'=> 'email'
                ]
            ])->setRequired(true)
            ->add('password', PasswordType::class, [
                'attr' => [
                    'autocomplete'=> 'current-password'
                ]
            ])->setRequired(true)
            ->add('login', SubmitType::class)->setRequired(true);
    }
}
