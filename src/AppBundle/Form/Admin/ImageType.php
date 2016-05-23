<?php

namespace AppBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path', 'text', [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control text-center',
                ],
            ])
            ->add('file', FileType::class, [
                'data_class' => 'AppBundle\Entity\Image',
                'label_attr' => [
                    'class' => 'btn green',
                ],
                'label' => 'Choisissez un fichier',
                'attr' => [
                    'class' => 'form-control inputfile',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Image',
        ));
    }
}