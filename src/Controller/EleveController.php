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

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $eleve = new Eleve();
        $form = $this->createForm(EleveFormType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $eleve->setPassword(
                $elevePasswordHasher->hashPassword(
                    $eleve,
                    $form->get('plainPassword')->getData()
                )
            );
            $eleve->setRoles(['ROLE_ELEVE']);
            $entityManager->persist($eleve);
            $entityManager->flush();
            // do anything else you need here, like send an email

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
        $entityManager->remove($eleve);
        $entityManager->flush();
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
        $eleve = $doctrine->getRepository(Eleve::class)->find($id);
        
        $form= $this->createForm(EleveFormType::class,$eleve);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $eleve->setCodeMassar($form->get('codeMassar')->getData());
            $eleve->setNom($form->get('nom')->getData());
            $eleve->setPrenom($form->get('prenom')->getData());
            $eleve->setEmail($form->get('email')->getData());
            $eleve->setAdresse($form->get('adresse')->getData());
            $eleve->setSexe($form->get('sexe')->getData());
            $eleve->setDateNaissance($form->get('dateNaissance')->getData());
            $eleve->setLieuNaissance($form->get('lieuNaissance')->getData());
            $eleve->setTel($form->get('tel')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('list_eleve');
        }
        return $this->render('eleve/modifierEleve.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }
}