<?php

namespace App\Controller;

use App\Entity\SuiviPedagogique;
use App\Form\SuiviPedaFormType;
use App\Repository\SuiviPedagogiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/suivipedagogique')]
class SuiviPedagogiqueController extends AbstractController
{
    #[Route('/', name: 'app_suivi_index', methods: ['GET'])]
    public function index(SuiviPedagogiqueRepository $suiviPedagogiqueRepository): Response
    {
        return $this->render('suivi_pedagogique/index.html.twig', [
            'suivis' => $suiviPedagogiqueRepository->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_suivi_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager ): Response
    {
        $suivi = $entityManager->getRepository(SuiviPedagogique::class)->find($id);
        return $this->render('suivi_pedagogique/show.html.twig', [
            'suivi' => $suivi,
        ]);
    }

    #[Route('/addsuivipedagogique', name: 'add_suivi_pedagogique')]
    public function addSuivi(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $suivi = new SuiviPedagogique();
        $form = $this->createForm(SuiviPedaFormType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pdf = $form->get('fichier')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pdf) {
                $originalFilename = pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdf->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pdf->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $suivi->setFichier($newFilename);
            }
            $entityManager->persist($suivi);
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index');
        }

        return $this->render('suivi_pedagogique/new.html.twig', [
            'SuiviPedaFormType' =>  $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_edit', methods: ['GET','POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $suivi = $entityManager->getRepository(SuiviPedagogique::class)->find($id);
        $form = $this->createForm(SuiviPedaFormType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suivi_pedagogique/edit.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_delete', methods: ['DELETE','POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {   
        $suivi = $entityManager->getRepository(SuiviPedagogique::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$suivi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($suivi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
    }

}
