<?php

namespace App\Controller;

use App\Entity\NivScol;
use App\Form\NivScolType;
use App\Repository\NivScolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/niv/scol')]
class NivScolController extends AbstractController
{
    #[Route('/', name: 'app_niv_scol_index', methods: ['GET'])]
    public function index(NivScolRepository $nivScolRepository): Response
    {
        return $this->render('niv_scol/index.html.twig', [
            'niv_scols' => $nivScolRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_niv_scol_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NivScolRepository $nivScolRepository): Response
    {
        $nivScol = new NivScol();
        $form = $this->createForm(NivScolType::class, $nivScol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nivScolRepository->save($nivScol, true);

            return $this->redirectToRoute('app_niv_scol_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('niv_scol/new.html.twig', [
            'niv_scol' => $nivScol,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_niv_scol_show', methods: ['GET'])]
    public function show(NivScol $nivScol): Response
    {
        return $this->render('niv_scol/show.html.twig', [
            'niv_scol' => $nivScol,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_niv_scol_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NivScol $nivScol, NivScolRepository $nivScolRepository): Response
    {
        $form = $this->createForm(NivScolType::class, $nivScol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nivScolRepository->save($nivScol, true);

            return $this->redirectToRoute('app_niv_scol_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('niv_scol/edit.html.twig', [
            'niv_scol' => $nivScol,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_niv_scol_delete', methods: ['POST'])]
    public function delete(Request $request, NivScol $nivScol, NivScolRepository $nivScolRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nivScol->getId(), $request->request->get('_token'))) {
            $nivScolRepository->remove($nivScol, true);
        }

        return $this->redirectToRoute('app_niv_scol_index', [], Response::HTTP_SEE_OTHER);
    }
}
