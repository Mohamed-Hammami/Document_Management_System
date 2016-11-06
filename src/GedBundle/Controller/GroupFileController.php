<?php

namespace GedBundle\Controller;

use GedBundle\Entity\GroupeFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GedBundle\Form\GroupeFileType;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GroupFileController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    public function addAction(Request $request, $id)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to add a permission !');
//        $user =  $token = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $groupFile = new GroupeFile();

        $groupeRepository = $em->getRepository('GedBundle:Groupe');
        $groups = $groupeRepository->findFreeGroups($id);

        $form = $this->createForm(GroupeFileType::class, $groupFile, array( 'groups' => $groups ));

        $form->add('confirm', 'submit', array(
            'label' => 'confirm',
            'attr' => array(
                'class' => 'btn btn-primary'
            )));
//            ->add('cancel', 'reset', array(
//                'label' => 'cancel',
//                'attr' => array(
//                    'class' => 'btn btn-primary'
//                )));

        $fileRepository = $em->getRepository('GedBundle:File');

        $file = $fileRepository->find($id);

        $form->handleRequest($request);
        if( $form->isValid() )
        {

            $groupFile->setFile($file);
            $em->persist($groupFile);
            $em->flush();

            return $this->redirect($this->generateUrl('file_show', array('id' => $file->getId()))) ;

        }

        return $this->render(
            'GedBundle:CRUD:permissionCreate.html.twig',
            array(
                'form' => $form->createView(),
                'file' => $file
            )
        );
    }

    public function changeAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to chnage a permission !');

        $em = $this->getDoctrine()->getEntityManager();

        $groupFileRepository = $em->getRepository('GedBundle:GroupeFile');

        if( ! $permission = $groupFileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no permission with %d id', $id));
        }

        $level = $request->get('level');

        if ( $level > 2 || $level < 0 )
        {
            throw new ResourceNotFoundException( sprintf('Wrong level value'));
        }

        $permission->setLevel($level);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function removeAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to remove a permission !');

        $em = $this->getDoctrine()->getEntityManager();

        $groupFileRepository = $em->getRepository('GedBundle:GroupeFile');

        if( ! $permission = $groupFileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no permission with %d id', $id));
        }

        $em->remove($permission);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }
}
