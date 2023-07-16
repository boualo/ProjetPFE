<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\RegistrationFormType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    
    private $adminRepo;
    public function __construct(AdminRepository $adminRepository,private RequestStack $requestStack,){
        $this->adminRepo = $adminRepository;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $session = $this->requestStack->getSession();
        $user = new Admin();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $title= $session->get('title');
            
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $role= $session->get('currRole');
            $user->setRoles([$role]);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admins_by_role',[
                'role'=>$role,
                'title' => $title
            ]);;
        }

        return $this->render('registration/connexion.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => $title
        ]);
    }

    #[Route('/admins/{role}', name:"admins_by_role")]
    public function getAdminsByRole($role, EntityManagerInterface $entityManager)
    {
        $session = $this->requestStack->getSession();
        switch($role) {
            case 'admin':
                $role='ROLE_ADMIN';
                break;
            case 'direct':
                $role='ROLE_DIRECTEUR';
                break;
            case 'administ':
                $role='ROLE_ADMINISTRATEUR';
                break;
            case 'EP':
                $role='ROLE_ENCADREMENTP';
                break;
            case 'ensei':
                $role='ROLE_ENSEIGNANT';
                break;
            default :
                $role=$role;
                break;
            
        }
        $adminRepository = $entityManager->getRepository(Admin::class);

        $session->set('currRole',$role);
        $session->set('title',$this->getTitle($role));

        $admins = $adminRepository->createQueryBuilder('a')
            ->where('a.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
            return $this->render('registration/admins.html.twig', [
                'admins' => $admins,
                'title' => $this->getTitle($role)
            ]);
    }

    #[Route('/Modifier/{id}',name:"show_admin")]
    public function show($id) {
        $session = $this->requestStack->getSession();
        $admin=$this->adminRepo->find($id);
        $title = $session->get('title');
        
        return $this->render('registration/connexion.html.twig',[
            'admin' => $admin,
            'title' => $title
        ]);
    }

    #[Route('/admin/delete/{id}' , name:'delete')]
    public function delete(int $id,EntityManagerInterface $entityManager) : RedirectResponse{
        $session = $this->requestStack->getSession();
        
        $admin = $entityManager->getRepository(Admin::class)->find($id);
        $entityManager->remove($admin);
        $entityManager->flush();
        $role= $session->get('currRole');
        $title = $session->get('title');
        return $this->redirectToRoute('admins_by_role',[
            'role'=>$role,
            'title' => $title
        ]);
    }


    public function getTitle($role){
        $titlePage = "";
        switch($role) {
            case 'ROLE_ADMIN':
                $titlePage="Admin";
                break;
            case 'ROLE_DIRECTEUR':
                $titlePage="Directeur";
                break;
            case 'ROLE_ADMINISTRATEUR':
                $titlePage="Administrateurs";
                break;
            case 'ROLE_ENCADREMENTP':
                $titlePage="Encadrement Pédagogique";
                break;
            case 'ROLE_ENSEIGNANT':
                $titlePage="Enseignants";
                break;
        }
        return $titlePage;
    }
}
