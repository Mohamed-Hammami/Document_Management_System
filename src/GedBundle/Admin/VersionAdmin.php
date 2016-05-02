<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 31/03/2016
 * Time: 10:16
 */

namespace GedBundle\Admin;

use GedBundle\Entity\Version;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Vich\UploaderBundle\Form\Type\VichFileType;

class VersionAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('type')
            ->add('fileContent', 'file', array(
                'required' => false
            ))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('type')
            ->add('fileName', null, array(
                'label' => 'Original Name'
            ))
            ->add('created')
            ->add('createdBy')
            ->add('size', null, array('code' => 'getFormattedSize'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('type')
            ->add('fileName', null, array(
                'label' => 'Original Name'
            ))
            ->add('created')
            ->add('createdBy')
            ->add('size', null, array('code' => 'getFormattedSize'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }


    public function preUpdate($version)
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if( $user->getId())
        {
            $version->setUpdatedBy($user);
        }
    }

    public function prePersist($version)
    {
        $size = $version->getFileContent()->getSize();
        $version->setSize($size);

        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if( $user->getId())
        {
            $version->setUpdatedBy($user);
        }
    }
}