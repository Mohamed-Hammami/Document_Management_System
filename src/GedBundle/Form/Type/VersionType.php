<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 26/04/2016
 * Time: 11:07
 */

namespace GedBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VersionType extends  AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileContent', 'file', array(
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GedBundle\Entity\Version',
        ));
    }
}