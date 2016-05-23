<?php
namespace AppBundle\Form\Admin;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxTypace;


class PresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', 'text', [
            'label' => 'Journaliste',
            'attr' => [
                'class' => 'form-control',
            ],
        ])->add('entreprise', 'text', [
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
        ])->add('titre', 'text', [
            'label' => 'Titre',
            'attr' => [
                'class' => 'form-control input-lg',
            ],
        ])->add('texte', 'textarea', [
            'label' => 'Soyez créatif !',
            'attr' => [
                'rows' => 13,
                'class' => 'form-control',
            ],
        ])->add('url', 'url', [
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