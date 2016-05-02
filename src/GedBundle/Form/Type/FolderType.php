<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 26/04/2016
 * Time: 14:52
 */

namespace GedBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Folder name',
                'attr' => array(
                    'placeholder' => 'Enter a folder name',
                    'class' => 'form-control'
                )))
            ->add('description', 'textarea', array(
                'label' => 'Description',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Enter a description',
                    'class' => 'form-control'
                )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'GedBundle\Entity\Folder'
        ));
    }
}