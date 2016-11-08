<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 25/04/2016
 * Time: 15:49
 */

namespace GedBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    public function  buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', null, array(
                'label' => 'File name',
                'attr' => array(
                    'placeholder' => 'Enter a file name',
                    'class' => 'form-control'
            )))
            ->add('nature', null, array(
                'label' => 'Nature',
                'attr' => array(
                    'class' => 'form-control'
            )))
            ->add('description', 'textarea', array(
                'label' => 'Description',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Enter a description',
                    'class' => 'form-control'
            )))
            ->add('version', VersionType::class,
                array('required' => false,
                        'disabled' => $options['edit']? true: false,
                        'mapped' => false,
                        'label' => 'First version'
                ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
           'data_class' => 'GedBundle\Entity\File',
            'edit' => false,
        ));
    }


}