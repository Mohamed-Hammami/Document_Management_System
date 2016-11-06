<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 31/05/2016
 * Time: 19:40
 */

namespace GedBundle\Security;


use Doctrine\ORM\EntityManager;
use GedBundle\Entity\File;
use GedBundle\Entity\Groupe;
use GedBundle\Entity\GroupeFile;
use GedBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FileVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CONTROL = 'control';

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function supports($attribute, $subject)
    {
        if( !in_array($attribute, array(self::VIEW, self::EDIT, self::CONTROL)) )
        {
            return false;
        }

        if( !$subject instanceof File )
        {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof User)
        {
            return false;
        }

        $file = $subject;

        $groupRepository = $this->em->getRepository('GedBundle:Groupe');
        $groupFileRepository = $this->em->getRepository('GedBundle:GroupeFile');

        $groups = $groupRepository->findGroupsByUser($user->getId());
        $permissions = $groupFileRepository->findGroupFileByFile($file->getId());


        switch($attribute){
            case self::VIEW:
                return $this->canView($file, $user, $groups, $permissions);
            case self::EDIT:
                return $this->canEdit($file, $user, $groups, $permissions);
            case self::CONTROL:
                return $this->canControl($file, $user, $groups, $permissions);
        }

        throw new \LogicException('This code should not be reached !');
    }

    private function canView(File $file, User $user, $groups, $permissions)
    {
        if( $this->canEdit($file, $user, $groups, $permissions) )
        {
            return true;
        }

        foreach( $permissions as $permission )
        {
            foreach($groups as $group)
            {
                if ( $this->campare($permission, $group, 2) )
                    return true;
            }
        }

        return false;
    }

    private function canEdit(File $file, User $user, $groups, $permissions)
    {
        if( $this->canControl($file, $user, $groups, $permissions) )
        {
            return true;
        }

        foreach( $permissions as $permission )
        {
            foreach($groups as $group)
            {
                if ( $this->campare($permission, $group, 1) )
                    return true;
            }
        }

        return false;

    }

    private function canControl(File $file, User $user, $groups, $permissions)
    {
        if( $user->getId() == $file->getCreatedBy()->getId() ){
            return true;
        }elseif( in_array('ROLE_ADMIN', $user->getRoles()) ){
            return true;
        }

        foreach( $permissions as $permission )
        {
            foreach($groups as $group)
            {
                if ( $this->campare($permission, $group, 0) )
                    return true;
            }
        }

        return false;

    }

    private function campare(GroupeFile $gf, Groupe $g, $level)
    {
        if( ( $gf->getGroupe()->getId() == $g->getId()) || ( $gf->getLevel() <= $level ))
        {
            return true;
        } else {
            return false;
        }
    }
}