<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 06/11/2016
 * Time: 16:41
 */

namespace GedBundle\Security;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutHandler implements LogoutSuccessHandlerInterface
{

    private  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onLogoutSuccess(Request $request)
    {
        // Routine to clean the clipboard

        if( $cutFiles = $request->getSession()->get('cutFiles')  )
        {
            $fileRepository = $this->em->getRepository('GedBundle:File');
            foreach( $cutFiles as $file )
            {
                if( $file = $fileRepository->find($file->getId()) )
                {
                    $file->setOnHold(false);
                    $file->setUpdatedBy($file->getUpdatedBy());
                }
            }
        }

        if( $cutFolders = $request->getSession()->get('cutFolders')  )
        {
            $folderRepository = $this->em->getRepository('GedBundle:Folder');
            foreach( $cutFolders as $folder )
            {
                if( $folder = $folderRepository->find($folder->getId()) )
                {
                    $folder->setOnHold(false);
                    $folder->setUpdatedBy($folder->getUpdatedBy());
                }
            }
        }

        $this->em->flush();
        $response = new RedirectResponse('login');
        return $response;

    }
}