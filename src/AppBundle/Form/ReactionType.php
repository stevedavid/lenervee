<?php
namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class ReactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'attr' => array(
                'placeholder' => 'Votre nom *',
            ),
        ))->add('email', 'text', array(
            'attr' => array(
                'placeholder' => 'Votre email *',
            ),
        ))->add('url', 'text', array(
            'required' => false,
            'attr' => array(
                'placeholder' => 'Votre site',
            ),
        ))->add('reaction', 'textarea', array(
            'attr' => array(
                'placeholder' => 'Exprimez-vous... *',

            ),
        ))->add('submit', 'submit', array(
            'label' => 'Réagir',
            'attr' => array(
                'class' => 'submit',
            ),
        ));

    }

    public function getName()
    {
        return 'reaction';
    }
}