<?php
namespace AppBundle\Form\Admin;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;


class PresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', Type\TextType::class, [
            'label' => 'Journaliste',
            'attr' => [
                'class' => 'form-control',
            ],
        ])->add('entreprise', Type\TextType::class, [
            'label' => 'Média',
            'attr' => [
                'class' => 'form-control',
            ],
        ])->add('date', DateTimeType::class, [
            'widget' => 'text',
            'html5' => false,
            'input' => 'datetime',
            'format' => 'yyyy-MM-dd kk:mm',
            'attr' => [
                'class' => 'form-control hidden'
            ],
        ])->add('titre', Type\TextType::class, [
            'label' => 'Titre',
            'attr' => [
                'class' => 'form-control input-lg',
            ],
        ])->add('texte', Type\TextareaType::class, [
            'label' => 'Soyez créatif !',
            'attr' => [
                'rows' => 13,
                'class' => 'form-control',
            ],
        ])->add('url', Type\UrlType::class, [
            'label' => 'Commence par http://',
            'required' => false,
            'attr' => [
                'pattern' => 'https?://.+',
                'class' => 'form-control',
            ],
        ])->add('submit', SubmitType::class, [
            'label' => 'Sauvegarder',
            'attr' => [
                'class' => 'btn blue',
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'presse';
    }
}