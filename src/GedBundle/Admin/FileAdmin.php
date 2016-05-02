<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 30/03/2016
 * Time: 17:00
 */

namespace GedBundle\Admin;

use GedBundle\Entity\File;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class FileAdmin extends  Admin
{

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('versions', 'sonata_type_model', array(
                'attr' => array(
                    'data-sonata-select2-allow-clear' => 'true'
                )
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {

//        Set the folder as a parent folder from the address from the referer

        $referer = $this->getRequest()->headers->get('referer');
        $id = $this->parseReferer($referer);

        if( is_integer($id))
        {

        }

        dump($id);

        $formMapper
            ->with('General')
                ->add('name')
                ->add('folder', 'sonata_type_model')
            ->end()
            ->with('Versions')
                ->add('versions', 'sonata_type_collection', array(
                    'required' => false,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
            ->end()
        ;

    }

    /**
     * param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('created')
            ->add('updated')
            ->add('versions', 'many_to_one', array(
                'associated_property' => 'name'
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }


    public function preUpdate($file)
    {
        foreach ( $file->getVersions() as $versions)
        {
            $versions->setFile($file);
        }
    }

    public function prePersist($file)
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $file->setCreatedBy($user);

    }

    /**
     * @param string $referer
     *
     */
    public function parseReferer($referer)
    {

//        this function needs to be modified it's very bad :( I have no internet connexion

        $path = parse_url($referer);
        $path = $path['path'];

        $correctValues = array("", "admin", "ged", "folder", "", "folderList");
        $values = explode('/', $path);

        if (count($correctValues) == count($values))
            if ($correctValues[0] == $values[0] and
                $correctValues[1] == $values[1] and
                $correctValues[2] == $values[2] and
                $correctValues[3] == $values[3] and
                $correctValues[5] == $values[5])
                    return ( $values[4] + 0 );

        dump($values);
        dump($correctValues);
    }

}