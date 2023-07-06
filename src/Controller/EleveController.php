<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\EleveFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EleveController extends AbstractController{

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
            $eleve->setRoles([$_POST['roles']]);
            $entityManager->persist($eleve);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/ajouterEleve.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}