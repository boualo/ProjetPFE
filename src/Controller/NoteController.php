<?php

namespace App\Controller;

use App\Repository\FiliereRepository;
use App\Repository\GroupRepository;
use App\Repository\NivScolRepository;
use App\Repository\SousNiveauScolRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class NoteController extends AbstractController
{
    private $repoFiliere;
    private $repoNivScol;
    private $repoGroupe;
    private $repoSousNivScol;
    public function __construct(FiliereRepository $repoFiliere,NivScolRepository $repoNivScol,SousNiveauScolRepository $repoSousNivScol,GroupRepository $repoGroupe){
        $this->repoFiliere = $repoFiliere;
        $this->repoNivScol = $repoNivScol;
        $this->repoSousNivScol = $repoSousNivScol;
        $this->repoGroupe = $repoGroupe;
    }
    #[Route('/note', name: 'app_note')]
    public function index(): Response
    {
        $schoolLevels = $this->repoSousNivScol->findAll();
        return $this->render('note/index.html.twig', [
            'schoolLevels' => $schoolLevels,

        ]);
    }
    // #[Route('/note_valide', name: 'app_valide')]
    // public function note_valide(Request $request): Response
    // {
    //     dd($request->get('idSousNiveau'));
    //     return $this->render('note/index.html.twig');
    // }
    /**
     * @Route("/get_filieres", name="get_filieres", methods={"POST"})
     */
    public function getFilieres(Request $request): JsonResponse
    {
        $schoolLevelId = $request->request->get('school_level_id');
        $schoolLevel = $this->repoSousNivScol->find($schoolLevelId);

        $filieres = $schoolLevel->getFilieres();
        $filiereData = [];

        foreach ($filieres as $filiere) {
            $filiereData[] = [
                'id' => $filiere->getId(),
                'name' => $filiere->getNom(),
            ];
        }

        return new JsonResponse($filiereData);
    }
    /**
     * @Route("/get_groupes", name="get_groupes", methods={"POST"})
     */
    public function getGroupes(Request $request): JsonResponse
    {
        $schoolLevelId = $request->request->get('school_level_id');
        $schoolLevel = $this->repoFiliere->find($schoolLevelId);

        $groupes = $schoolLevel->getIdGroup();
        $filiereData = [];

        foreach ($groupes as $groupe) {
            $filiereData[] = [
                'id' => $groupe->getId(),
                'name' => $groupe->getNomGroup(),
            ];
        }
        return new JsonResponse($filiereData);
    }
    
}
