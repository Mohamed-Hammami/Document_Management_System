<?php

namespace GedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'Username',
                'disabled' => $options['edit']? true: false,
                'attr' => array(
                    'placeholder' => 'Enter a username',
                    'class' => 'form-control'
            )))
            ->add('name', null, array(
                'label' => 'Name',
                'attr' => array(
                    'placeholder' => 'Enter your name',
                    'class' => 'form-control'
                )))
            ->add('surname', null, array(
                'label' => 'Surname',
                'attr' => array(
                    'placeholder' => 'Enter your surname',
                    'class' => 'form-control'
                )))
            ->add('title', null, array(
                'label' => 'Title or Post',
                'attr' => array(
                    'placeholder' => 'Enter your title',
                    'class' => 'form-control'
                )))
            ->add('email', 'email', array(
                'label' => 'Email',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Enter your email',
                    'class' => 'form-control'
            )))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'label' => 'Set a password for the user',
                'options' => array(),
                'first_options' => array('label' => 'Password', 'attr' => array(
                    'placeholder' => 'Enter a password',
                    'class' => 'form-control')),
                'second_options' => array('label' => 'Password confirmation', 'attr' => array(
                    'placeholder' => 'Enter a password',
                    'class' => 'form-control')),
                'invalid_message' => 'The passwords don\'t match',
            ));


            if( $options['edit']  )
            {
                $builder->add('avatarEdit', 'file', array(
                    'label' => 'Avatar',
                    'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Add your avatar',
                        'class' => 'form-control'
                    )));
            } else {
                $builder->add('avatar', 'file', array(
                    'label' => 'Avatar',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Add your avatar',
                        'class' => 'form-control'
                    )));

            }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'edit' => false,
                'firstLogin' => false,
                'data_class' => 'GedBundle\Entity\User',
                'allow_extra_fields' => true,

            )
        );

    }
}
