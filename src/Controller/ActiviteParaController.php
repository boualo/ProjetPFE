<?php

namespace App\Controller;

use App\Entity\ActivitePara;
use App\Form\ActiviteParaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActiviteParaController extends AbstractController
{
    private $activiteRepo;

    #[Route('/ajoutActivite', name: 'add_activite_para')]
    public function add_activite(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        if(!$this->getUser())
            return $this->redirectToRoute('app_login') ;
        $activite = new ActivitePara();
        $form = $this->createForm(ActiviteParaType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('activite_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $activite->setImage($newFilename);
            }
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('activites');
        }

        return $this->render('activite_para/ajouterActivite.html.twig', [
            'activiteForm' =>  $form->createView(),
        ]);
    }

    #[Route('/activites', name:"activites")]
    public function getActivites(EntityManagerInterface $entityManager)
    {
        $activiteParaRepository = $entityManager->getRepository(ActivitePara::class);

        $activites = $activiteParaRepository->createQueryBuilder('a')
            ->select('a.id, a.titre, a.description, a.image')
            ->getQuery()
            ->getResult();
            return $this->render('activite_para/activites.html.twig', [
                'activites' => $activites,
            ]);
    }

    #[Route('/activite/{id}' , name:'delete_activite')]
    public function delete($id,EntityManagerInterface $entityManager) : RedirectResponse{
        $activite = $entityManager->getRepository(ActivitePara::class)->find($id);
        $entityManager->remove($activite);
        $entityManager->flush();
        return $this->redirectToRoute('activites');
    }

    #[Route("activites/activite/{id}", name:'activite')]

    public function show($id){
        $activite=$this->activiteRepo->find($id);
        return $this->render('activites/modifierActivite.html.twig',[
            'activite' => $activite,
        ]);
    }

    #[Route('/edit_activite/{id}' , name:'edit_activite')]
    public function edit($id,Request $request,ManagerRegistry $doctrine){
        $activite = $doctrine->getRepository(ActivitePara::class)->find($id);
        
        $form= $this->createForm(EleveFormType::class,$activite);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $activite= $form->getData();
            $manager=$doctrine->getManager();
            $manager->persist($activite);
            $manager->flush();

            return $this->redirectToRoute('activites');
        }
        return $this->render('activite/modifierActivite.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }
}
