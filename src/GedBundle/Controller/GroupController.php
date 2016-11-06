<?php

namespace GedBundle\Controller;

use GedBundle\Entity\Groupe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GedBundle\Form\GroupType;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GroupController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to access to groups\' list !');
        $user = $token = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $groupRepository = $em->getRepository('GedBundle:Groupe');

        $groups = $groupRepository->findGroupUsersNum();

        dump($groups);

        return $this->render(
            'GedBundle:CRUD:groupList.html.twig',
            array(
                'groups' => $groups,
                'user'   => $user,
            )
        );


    }

    public function showAction(Request $request, $groupid)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to access to groups !');
        $user = $token = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $groupRepository = $em->getRepository('GedBundle:Groupe');
        $userRepository = $em->getRepository('GedBundle:User');

        if( !$group = $groupRepository->findGroupsUsers($groupid))
        {
            throw new ResourceNotFoundException( sprintf('There is no user with %d id', $groupid));
        }

        $freeUsers = $userRepository->findFreeUsers($groupid);


        return $this->render(
            'GedBundle:CRUD:groupShow.html.twig',
            array( 'group'      => $group,
                   'user'       => $user,
                   'freeUsers'  => $freeUsers,
            )
        );

    }

    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to add a new user !');
        $user =  $token = $this->get('security.token_storage')->getToken()->getUser();

        $group = new Groupe();

        $form = $this->createForm(GroupType::class, $group);

        $form->add('create', 'submit', array(
            'label' => 'create',
            'attr' => array(
                'class' => 'btn btn-primary'
            )))
            ->add('cancel', 'reset', array(
                'label' => 'cancel',
                'attr' => array(
                    'class' => 'btn btn-primary'
                )));

        $form->handleRequest($request);
        if( $form->isValid() )
        {
            $em = $this->getDoctrine()->getManager();
            $groupRepository = $em->getRepository('GedBundle:Groupe');

            $users = $group->getUsers();

            foreach( $users as $user )
            {
                $user->addGroupes($group);
            }

            $em->persist($group);
            $em->flush();

            return $this->redirect($this->generateUrl('group_list'));
        }

        return $this->render(
            'GedBundle:CRUD:createGroup.html.twig',
            array(
              'form' => $form->createView(),
              'user' => $user,
          )
        );
    }

    public function fetchAction()
    {
        $em = $this->getDoctrine()->getManager();

        $groupRepository = $em->getRepository('GedBundle:Groupe');

        $groups = $groupRepository->findGroups();

        dump($groups);


        $response = new JsonResponse();

        $response->setData($groups);


        return $response;
    }

    public function userRemoveAction(Request $request, $groupid)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to remove a user from a group !');

        $em = $this->getDoctrine()->getEntityManager();

        $groupRepository = $em->getRepository('GedBundle:Groupe');
        $userRepository = $em->getRepository('GedBundle:User');
        $userId = $request->get('userId');

        if( ! $group = $groupRepository->find($groupid) )
        {
            throw new ResourceNotFoundException( sprintf('There is no group with %d id', $groupid));
        }

        if( ! $user = $userRepository->find($userId) )
        {
            throw new ResourceNotFoundException( sprintf('There is no group with %d id', $userId));
        }

        $user->removeGroupes($group);

        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

    public function removeAction(Request $request, $groupid)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to remove a user from a group !');

        $em = $this->getDoctrine()->getEntityManager();

        $groupRepository = $em->getRepository('GedBundle:Groupe');
        $groupFileRepository = $em->getRepository('GedBundle:GroupeFile');

        if( ! $group = $groupRepository->find($groupid) )
        {
            throw new ResourceNotFoundException( sprintf('There is no group with %d id', $groupid));
        }

        $users = $groupRepository->findUsersByGroup($groupid)->getUsers();
        $groupFiles = $groupFileRepository->findGroupFileByGroup($groupid);

        foreach($users as $user)
        {
            $user->removeGroupes($group);
        }

        foreach( $groupFiles as $groupFile )
        {
            $em->remove($groupFile);
        }

        $em->remove($group);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }
}
