<?php

namespace GedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
                        'label' => 'Group name',
                        'attr' => array(
                        'placeholder' => 'Enter a group name',
                        'class' => 'form-control'
                     )))
                    ->add('label', null, array(
                        'label' => 'Group label',
                        'attr' => array(
                        'placeholder' => 'Enter a group name',
                        'class' => 'form-control'
                     )))
                    ->add('users', Select2EntityType::class, [
                        'multiple' => true,
                        'class'    => 'GedBundle\Entity\User',
                        'minimum_input_length' => 2,
                        'placeholder' => 'Choose a user',
                        'remote_route' => 'user_fetch',
                        'primary_key' => 'id',
                        'text_property' => 'username',
                        'allow_clear' => true,

                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ged_bundle_group_type';
    }
}
