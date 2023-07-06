<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\RegistrationFormType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private $adminRepo;
    public function __construct(AdminRepository $adminRepository){
        $this->adminRepo = $adminRepository;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = new Admin();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //$user->setRoles([$_POST['roles']]);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/connexion.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // #[Route('/admins' , 'app_admins')]
    // public function getAdmins(String $role) : Response{
    //     $admins = $this->adminRepo->findAllByRole($role);
    //     return $this->render('admin/admins.html.twig', [
    //         'admins' => $admins
    //     ]);
    // }

    #[Route('/admins/{role}', name:"admins_by_role")]
    public function getAdminsByRole($role, EntityManagerInterface $entityManager)
    {
        if($role == 'admin')
            $role="ROLE_ADMIN";
        $adminRepository = $entityManager->getRepository(Admin::class);
        
        $admins = $adminRepository->createQueryBuilder('a')
            ->where('a.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
        
            return $this->render('registration/admins.html.twig', [
                'admins' => $admins
            ]);
    }

    #[Route('/admin/delete/{id}' , name:'delete')]
    public function delete($id){
        $this->adminRepo->remove($id);

        return $this->redirectToRoute('admins_by_role');
    }
}
