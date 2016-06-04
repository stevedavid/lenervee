<?php
namespace AppBundle\Form\Admin;

use AppBundle\Form\TagType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CourrierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control input-lg',
                ],
            ])->add('intro', TextType::class, [
                'label' => 'Accroche',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])->add('slug', TextType::class, [
                'label' => 'Slug',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])->add('courrier', TextareaType::class, [
                'attr' => [
                    'rows' => 58,
                    'class' => 'form-control'
                ],
            ])->add('envoi', DateTimeType::class, [
                'widget' => 'text',
                'html5' => false,
                'input' => 'datetime',
                'format' => 'yyyy-MM-dd kk:mm',
                'attr' => [
                    'class' => 'form-control hidden'
                ],
            ])->add('recu', DateTimeType::class, [
                'required' => false,
                'widget' => 'text',
                'html5' => false,
                'input' => 'datetime',
                'format' => 'yyyy-MM-dd kk:mm',
                'attr' => [
                    'class' => 'form-control hidden'
                ],
            ])->add('reponse', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 58,
                    'class' => 'form-control',
                ],
            ])->add('categorie', EntityType::class, array(
                'class' => 'AppBundle:Categorie',
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            ))->add('image', ImageType::class, array(
                'image_path' => 'path',
                'required' => false,
                'label_attr' => [
                    'class' => 'btn green',
                ],
                'label' => 'Choisissez un fichier',
                'attr' => [
                    'class' => 'form-control inputfile',
                ],
            ))->add('published', CheckboxType::class, array(
                'required' => false,
                'attr' => [
                    'class' => 'md-check',
                ],
            ))->add('tags', EntityType::class, [
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'class' => 'AppBundle:Tag',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])->add('submit', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => [
                    'class' => 'btn blue',
                ],
            ]);
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'AppBundle\Entity\Courrier',
        ));
    }

    public function getName()
    {
        return 'courrier';
    }
}