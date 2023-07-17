<?php

namespace App\Controller;

use App\Form\EleveFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }


    #[Route('/profile', name: 'app_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if(!$this->getUser())
            $this->redirectToRoute('app_login');
        $currentUser = $this->getUser();
        if($currentUser->getRoles()[0]=="ROLE_ADMIN")
            $userForm = $this->createForm(RegistrationFormType::class, $currentUser);
        else
            $userForm = $this->createForm(EleveFormType::class, $currentUser);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted()) {
            $photo = $userForm->get('photo')->getData();
            
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('profile_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $currentUser->setPhoto($newFilename);
            }

            $entityManager->persist($currentUser);
            $entityManager->flush();

           
        }

        return $this->render('profile/index.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
    
    #[Route('/profile/edit_pwd', name:"edit_password")]
    public function edit_pwd(Request $request,UserPasswordHasherInterface $passwordHasher)  : Response{
        $user = $this->getUser();
        dd($user->getPassword(),$request->get('actPwd'),password_verify($user->getPassword(), $request->get('actPwd')));
        
        return $this->render('profile');
    }
}
