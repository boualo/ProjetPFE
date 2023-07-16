<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Eleve;
use App\Entity\user;
use App\Form\EleveFormType;
use App\Form\userFormType;
use App\Repository\AdminRepository;
use App\Repository\userRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        
        return $this->render('profile/index.html.twig');
    } 

    #[Route('/edit-profile', name: 'edit_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $currentUser = $this->getUser();
        $userForm = $this->createForm(EleveFormType::class, $currentUser);
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

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
    
}
