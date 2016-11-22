<?php

namespace GedBundle\Controller;

use GedBundle\Entity\User;
use GedBundle\Entity\Workspace;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use GedBundle\Form\AddUserType;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserController extends Controller
{

    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));

    }


    public function profileAction(Request $request, $userid)
    {
        $token = $this->get('security.token_storage')->getToken();

        if( !$token->isAuthenticated() )
        {
            throw new AccessDeniedException('You need to be authentified to acces to profile');
        }


        $user = $token->getUser();

        return $this->render(
            'GedBundle:CRUD:profileShow.html.twig',
            array( 'user' => $user, 'owner' => true )
        );

    }

    public function showAction(Request $request, $userid)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to access to another user\'s profile !');

        $userManager = $this->get('fos_user.user_manager');

        if( !$user = $userManager->findUserBy(array('id' => $userid ))  )
        {
            throw new ResourceNotFoundException( sprintf('There is no user with %d id', $userid));
        }

        return $this->render(
            'GedBundle:CRUD:userShow.html.twig',
            array( 'user' => $user,
                    'owner' => false, )
        );

    }

    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to access to users\' list !');
        $user = $token = $this->get('security.token_storage')->getToken()->getUser();

        $userManager = $this->get('fos_user.user_manager');

        $users = $userManager->findUsers();

        dump($users);

        return $this->render(
            'GedBundle:CRUD:userList.html.twig',
            array( 'users' => $users,
                    'user' => $user
            )
        );
    }

    public function addAction(Request $request)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to add a new user !');

        $user = new User();

        $form = $this->createForm(AddUserType::class, $user);
        $form->add('create', 'submit', array(
            'label' => 'create',
            'attr' => array(
                'class' => 'btn btn-primary'
            )))
            ->add('cancel', 'reset', array(
                'label' => 'cancel',
                'attr' => array(
                    'class' => 'btn btn-primary'
                )))
            ->add('admin', 'checkbox', array(
                'mapped' => false,
                'label' => 'Admin',
                'required' => false,
                'attr' => array(
                    'class' => 'minimal'
                )
            ));

        $form->handleRequest($request);
        if( $form->isValid() )
        {
            $userManager = $this->get('fos_user.user_manager');

            $exists1 = $userManager->findUserByEmail($user->getEmail());
            $exists2 = $userManager->findUserByUsername($user->getUsername());

            if ($exists1 instanceof User) {
                throw new HttpException(409, 'Email already taken');
            } else if ($exists2 instanceof User) {
                throw new HttpException(409, 'Username already taken');
            }

            $admin = $form["admin"]->getData();

            if( $admin )
            {
                $user->setRoles(array("ROLE_ADMIN"));
            } else
            {
                $user->setRoles(array("ROLE_USER"));
            }

            $user->setEnabled(true);
            $user->setRegistrationDate(new \DateTime('now'));

            if ( $file = $user->getAvatar() ) {
                $fileName = md5($user->getUsername().md5(uniqid()));
                $fileDir = $this->getParameter('avatar_path');
                $file->move($fileDir, $fileName);
                $user->setAvatar('user/'.$fileName);
            }

            $this->addUserWorkspace($user);
            $userManager->updateUser($user);

            return $this->redirect($this->generateUrl('user_list'));

        }

        return $this->render(
          'GedBundle:CRUD:userCreate.html.twig',
            array(
                'form' => $form->createView(),
                'edit' => false,
                'user' => $token = $this->get('security.token_storage')->getToken()->getUser(),
            )
        );
    }

    public function modifyAction(Request $request, $userid)
    {
        $token = $this->get('security.token_storage')->getToken();

        if( !$token->isAuthenticated() )
        {
            throw new AccessDeniedException('You need to be authentified to acces to profile');
        }


        $userManager = $this->get('fos_user.user_manager');

        $user1 = $token->getUser();
        $user2 = $userManager->findUserBy(array('id' => $userid));

        if( $admin = $user1 <> $user2 )
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an administrator to modify to another user\'s information !');
        }

        $form = $this->createForm(AddUserType::class, $user1, array( 'edit' => true ));
        $form->add('create', 'submit', array(
            'label' => 'edit',
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
            $user1->setUpdatedAt( new \DateTime('now') );


            if ( $file = $form->get('avatarEdit')->getViewData() ) {
                $fileName = md5($user2->getUsername().md5(uniqid()));
                $fileDir = $this->getParameter('avatar_path');
                $file->move($fileDir, $fileName);
                $user2->setAvatar('user/'.$fileName);
            }

            $userManager->updateUser($user2);


            return $this->redirect($this->generateUrl('ged_bundle_home'));

        }

        if ($admin)
        {
            return $this->render(
                'GedBundle:CRUD:userEdit.html.twig',
                array(
                    'form' => $form->createView(),
                    'user' => $user2,
                )
            );
        }
        else
        {
            return $this->render(
                'GedBundle:CRUD:profileModify.html.twig',
                array(
                    'form' => $form->createView(),
                    'user' => $user2,
                )
            );
        }

    }

    public function fetchUsersAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository('GedBundle:User');

        $users = $userRepository->findUsers();

        dump($users);


        $response = new JsonResponse();

        $response->setData($users);


        return $response;
    }

    private function addUserWorkspace(User $user)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $workspace = new Workspace();
        $workspace->setUser($user);
        $em->persist($workspace);
        $em->flush();

    }
}
