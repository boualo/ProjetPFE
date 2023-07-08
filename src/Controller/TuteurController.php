<?php

namespace App\Controller;

use App\Entity\Tuteur;
use App\Form\TuteurFormType;
use App\Repository\TuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TuteurController extends AbstractController{

    private $tuteurRepo;
    public function __construct(tuteurRepository $tuteurRepository,private RequestStack $requestStack,){
        $this->tuteurRepo = $tuteurRepository;
    }

    #[Route('/ajoutTuteur','add_tuteur')]
    public function add_tuteur(Request $request, UserPasswordHasherInterface $tuteurPasswordHasher, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $tuteur = new Tuteur();
        $form = $this->createForm(TuteurFormType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $tuteur->setPassword(
                $tuteurPasswordHasher->hashPassword(
                    $tuteur,
                    $form->get('plainPassword')->getData()
                )
            );
            $tuteur->setRoles(['ROLE_TUTEUR']);
            $entityManager->persist($tuteur);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('tuteurs');
        }

        return $this->render('tuteur/ajouterTuteur.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route('/tuteurs', name:"tuteurs")]
    public function getTuteurs(EntityManagerInterface $entityManager)
    {
        
        $tuteurRepository = $entityManager->getRepository(Tuteur::class);

        $tuteurs = $tuteurRepository->createQueryBuilder('t')
            ->select('t.id,t.titre,t.CIN,t.nom,t.prenom,t.adresse,t.tel,t.email') 
            ->where('t.roles LIKE :role')
            ->setParameter('role', '%"'.'ROLE_TUTEUR'.'"%')
            ->getQuery()
            ->getResult();
            // dd($tuteurs);
            return $this->render('tuteur/tuteurs.html.twig', [
                'tuteurs' => $tuteurs,
            ]);
    }

    #[Route('/tuteur/{id}' , name:'delete_tuteur')]
    public function delete($id,EntityManagerInterface $entityManager) : RedirectResponse{
        $tuteur = $entityManager->getRepository(Tuteur::class)->find($id);
        $entityManager->remove($tuteur);
        $entityManager->flush();
        return $this->redirectToRoute('tuteurs');
    }

    #[Route("tuteurs/tuteur/{id}", name:'tuteur')]

    public function show($id){
        $tuteur=$this->tuteurRepo->createQueryBuilder('t')
        ->select('t.id,t.titre,t.CIN,t.nom,t.prenom,t.adresse,t.tel,t.email,t.sexe') 
        ->where('t.id = :id')
        ->setParameter('id',$id)
        ->getQuery()
        ->getResult();
        return $this->render('tuteur/modifierTuteur.html.twig',[
            'tuteur' => $tuteur,
        ]);
    }
    #[Route('/edit_tuteur/{id}' , name:'edit_tuteur')]
    public function edit(Tuteur $tuteur,Request $request,ManagerRegistry $doctrine){
        $form= $this->createForm(TuteurType::class,$tuteur);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tuteur= $form->getData();
            $manager=$doctrine->getManager();
            $manager->persist($tuteur);
            $manager->flush();

            return $this->redirectToRoute('tuteurs');
        }
        return $this->render('tuteur/modifierTuteur.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}