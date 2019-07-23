<?php

namespace Labstag\Form\Admin;

use Labstag\Entity\Bookmark;
use Labstag\FormType\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookmarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('url');
        $builder->add('imageFile');
        $builder->add('content', WysiwygType::class);
        $builder->add('enable');
        $builder->add('refuser');
        $builder->add('tags');
        $builder->add('submit', SubmitType::class);
        unset($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Bookmark::class,
            ]
        );
    }
}