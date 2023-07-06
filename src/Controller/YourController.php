<?php

namespace App\Controller;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class YourController extends AbstractController
{
    /**
     * @Route("/admin", name="users_by_role")
     */
    #[Route('/admin/{role}', name:"users_by_role")]
    public function getUsersByRole($role, EntityManagerInterface $entityManager)
    {
        $userRepository = $entityManager->getRepository(Admin::class);
        
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
        
        return new Response('Users with role '.$role.': '.count($users));
    }
}