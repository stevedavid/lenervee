<?php
namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class ReactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class, array(
            'attr' => array(
                'placeholder' => 'Votre nom *',
            ),
        ))->add('email', Type\TextType::class, array(
            'attr' => array(
                'placeholder' => 'Votre email *',
            ),
        ))->add('url', Type\TextType::class, array(
            'required' => false,
            'attr' => array(
                'placeholder' => 'Votre site',
            ),
        ))->add('reaction', Type\TextareaType::class, array(
            'attr' => array(
                'placeholder' => 'Exprimez-vous... *',

            ),
        ))->add('submit', Type\SubmitType::class, array(
            'label' => 'Réagir',
            'attr' => array(
                'class' => 'submit',
            ),
        ));

    }
}