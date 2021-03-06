<?php

namespace Labstag\Form\Security;

use Labstag\Lib\AbstractTypeLibSecurity;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractTypeLibSecurity
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'username',
            TextType::class,
            ['label' => 'Username']
        );
        $builder->add(
            'password',
            PasswordType::class,
            ['label' => 'Password']
        );
        $builder->add(
            'remember_me',
            CheckboxType::class,
            [
                'label'    => 'Keep me logged in',
                'required' => false,
            ]
        );
        $builder->add(
            'submit',
            SubmitType::class,
            ['label' => 'Sign in']
        );
        unset($options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configure your form options here
        $resolver->setDefaults(
            ['csrf_token_id' => 'login']
        );
    }
}
