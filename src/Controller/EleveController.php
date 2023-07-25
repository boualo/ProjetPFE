<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\EleveFormType;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EleveController extends AbstractController{

    private $eleveRepo;
    public function __construct(EleveRepository $eleveRepository,private RequestStack $requestStack,){
        $this->eleveRepo = $eleveRepository;
    }

    #[Route('/ajoutEleve','add_eleve')]
    public function add_eleve(Request $request, UserPasswordHasherInterface $elevePasswordHasher, EntityManagerInterface $entityManager){

        if(!$this->getUser())
            return $this->redirectToRoute('app_login') ;
        $eleve = new Eleve();
        $form = $this->createForm(EleveFormType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // encode the plain password
            $eleve->setPassword(
                $elevePasswordHasher->hashPassword(
                    $eleve,
                    $form->get('codeMassar')->getData()
                )
            );
           $eleve->setRoles(['ROLE_ELEVE']);
            $entityManager->persist($eleve);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('success', 'Ajouter avec succès et le mot de passe est'. $form->get('codeMassar')->getData());
            return $this->redirectToRoute('eleves');
        }

        return $this->render('eleve/ajouterEleve.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route('/eleves', name:"eleves")]
    public function getEleves(EntityManagerInterface $entityManager)
    {
        
        $eleveRepository = $entityManager->getRepository(Eleve::class);

        $eleves = $eleveRepository->createQueryBuilder('e')
            ->select('e.id,e.codeMassar,e.dateNaissance,e.lieuNaissance,e.nom,e.prenom,e.adresse,e.tel,e.email')
            ->where('e.roles LIKE :role')
            ->setParameter('role', '%"'.'ROLE_ELEVE'.'"%')
            ->getQuery()
            ->getResult();
            return $this->render('eleve/eleves.html.twig', [
                'eleves' => $eleves,
            ]);
    }

    #[Route('/eleve/{id}' , name:'delete_eleve')]
    public function delete($id,EntityManagerInterface $entityManager) : RedirectResponse{
        $eleve = $entityManager->getRepository(Eleve::class)->find($id);
        if (!$eleve) {
            throw $this->createNotFoundException('Eleve avec l\'ID '.$id.' pas trouvé.');
        }

        // Retrieve the related Note entities
        $notes = $eleve->getNotes();

        // Remove the Eleve entity and its related Note entities
        $entityManager->remove($eleve);
        foreach ($notes as $note) {
            $entityManager->remove($note);
        }

        // Flush the changes to the database to perform the deletion
        $entityManager->flush();

        // Optionally, add a flash message to provide feedback to the user
        $this->addFlash('success', 'Eleve a été supprimé avec succès.');

        // $eleve->removeNote($eleve->getNom())
        return $this->redirectToRoute('eleves');
    }

    #[Route("eleves/eleve/{id}", name:'eleve')]

    public function show($id){
        $eleve=$this->eleveRepo->find($id);
        return $this->render('eleve/modifierEleve.html.twig',[
            'eleve' => $eleve,
        ]);
    }

    #[Route('/edit_eleve/{id}' , name:'edit_eleve')]
    public function edit($id,Request $request,ManagerRegistry $doctrine,EntityManagerInterface $entityManager){
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(Eleve::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User with ID '.$id.' not found.');
        }
        // dd($request);
        $form = $this->createForm(EleveFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // The form is valid, update the user entity with the submitted data
            $entityManager->flush();

            // Optionally, add a flash message to provide feedback to the user
            $this->addFlash('success', 'User updated successfully.');

            // Redirect the user to a different page or return a response
            return $this->redirectToRoute('eleves');
        }
        return $this->render('eleve/modifierEleve.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }
}