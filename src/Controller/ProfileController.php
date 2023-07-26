<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\ChangePasswordType;
use App\Form\EleveFormType;
use App\Form\EnseignantType;
use App\Form\RegistrationFormType;
use App\Form\TuteurFormType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
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
        if($currentUser->getRoles()[0]=="ROLE_ADMIN" or $currentUser->getRoles()[0]=="ROLE_SUPERADMIN")
            $userForm = $this->createForm(RegistrationFormType::class, $currentUser);
        elseif($currentUser->getRoles()[0]=="ROLE_ELEVE")
            $userForm = $this->createForm(EleveFormType::class, $currentUser);
        elseif($currentUser->getRoles()[0]=="ROLE_ENSEIGNANT")
            $userForm = $this->createForm(EnseignantType::class, $currentUser);
        else
            $userForm = $this->createForm(TuteurFormType::class, $currentUser);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
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
    public function editPassword(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $passwordEncoder): Response
    {
        if(!$this->getUser())
            return $this->redirectToRoute('app_login');
        $user = $this->getUser(); // Récupère l'utilisateur connectés
       // dd($user);
        // Récupère les données du formulaire
        $actPwd = $request->request->get('actPwd');
        $newPwd = $request->request->get('newPwd');
        $conNewPwd = $request->request->get('conNewPwd');

        // Vérifie si le formulaire a été soumis
        if ($request->isMethod('POST')) {
            // Vérifie si le mot de passe actuel correspond au mot de passe de l'utilisateur
            if ($passwordEncoder->isPasswordValid($user, $actPwd)) {
                //dd($request,'9dim');
                // Vérifie si les nouveaux mots de passe correspondent
                if ($newPwd === $conNewPwd) {
                    
                    // Met à jour le mot de passe de l'utilisateur
                    $user->setPassword($passwordEncoder->hashPassword($user,$newPwd));

                    // Enregistre les changements dans la base de données
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Le mot de passe a été modifié.');

                    // Redirige vers une page de confirmation de changement de mot de passe
                    return $this->redirectToRoute('app_profile');
                } else {
                    // Affiche un message d'erreur si les nouveaux mots de passe ne correspondent pas
                    $this->addFlash('danger', 'Les mots de passe ne sont pas identiques.');
                }
            } else {
                // Affiche un message d'erreur si le mot de passe actuel est incorrect
                $this->addFlash('danger', 'Mot de passe actuel incorrect.');
            }
        }

        return $this->redirectToRoute('app_profile');
    }

}
