<?php

namespace GedBundle\Form;

use GedBundle\Repository\GroupeFileRepository;
use GedBundle\Repository\GroupeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;


class GroupeFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('level', ChoiceType::class, array(
                'choices' => array(
                    2 => 'View',
                    1 => 'Edit',
                    0 => 'Control',
                ),
            ))
            ->add('groupe', EntityType::class, array(
                'class' => 'GedBundle\Entity\Groupe',
                'choice_label' => 'label',
                'label' => 'Group',
                'placeholder' => 'Choose a group',
                'choices' =>  $options['groups'],
//                'query_builder' => function( GroupeRepository $gr )
//                    {
//                        return $gr->findFreeGroups('fileId');
//                    }
            ));
//            ->add('groupe', Select2EntityType::class, [
//            'label' => 'Groups',
//            'class'    => 'GedBundle\Entity\Groupe',
//            'minimum_input_length' => 2,
//            'placeholder' => 'Choose a group',
//            'remote_route' => 'group_fetch',
//            'primary_key' => 'id',
//            'text_property' => 'label',
//            'allow_clear' => true,
//            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'groups' => null
        ));
    }

}
