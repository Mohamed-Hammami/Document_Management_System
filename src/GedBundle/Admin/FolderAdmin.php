<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 01/04/2016
 * Time: 14:59
 */

namespace GedBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class FolderAdmin extends Admin
{

    public function configure()
    {
        $this->setTemplate('folderList', 'GedBundle:CRUD:folderList.html.twig');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('parent')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('parent')
        ;
    }
}