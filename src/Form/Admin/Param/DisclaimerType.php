<?php

namespace Labstag\Form\Admin\Param;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Labstag\FormType\WysiwygType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class DisclaimerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'activate',
            ChoiceType::class,
            [
                'choices' => [
                    'Non' => '0',
                    'Oui' => '1',
                ],
            ]
        );
        $builder->add('title', TextType::class, ['required' => false]);
        $builder->add('message', WysiwygType::class, ['required' => false ]);
        $builder->add('url-redirect', UrlType::class, ['required' => false]);
        unset($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                // Configure your form options here
            ]
        );
    }
}
