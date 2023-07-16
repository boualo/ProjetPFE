<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Absence;
use App\Form\AbsenceFormType;
use App\Controller\EleveController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DateTimeImmutable;

class AbsenceController extends AbstractController
{
    // public function __constructor(private EntityManagerInterface $entityManager) {}
    #[Route('/absence', name: 'absence')]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $absence = new Absence();
        $form = $this->createForm(AbsenceFormType::class, $absence);
        $form->handleRequest($request);
        $stringDate = date('Y-m-d');
        $absenceN = null;
        $heurActuel = [ date('Y-m-d'), date("h:00:00"), date("h:00:00", strtotime("+1 hour"))];
        $state = gettype($absenceN);
        $eleveRepository = $entityManager->getRepository(Eleve::class);
        $eleves = $eleveRepository->findAllAbsentEleve();
        $absences = $eleveRepository->findAllAbsentParDate($stringDate);

        if ($form->isSubmitted()) {
            $date = $absence->getDateAbsence();
            $absenceN = $form->getData();
            $heurActuel[1] = $absence->getHeureDebut()->format('h:00:00');
            $heurActuel[2] = $absence->getHeureFin()->format('h:00:00');
            $stringDate = $date->format('Y-m-d');
            $absences = $eleveRepository->findAllAbsentParDate($stringDate);
            $eleves = $eleveRepository->findAllAbsentEleve();
            return $this->render('absence/index.html.twig', [
                'absenceForm' =>  $form->createView(),
                'absenceN' => $absenceN,
                'elevesabsence' => $eleves,
                'date' => $stringDate,
                'absences' => $absences,
                'heurActuel' => $heurActuel,
                // 'state' => $state,
            ]);
        }
        
        return $this->render('absence/index.html.twig', [
            'absenceForm' =>  $form->createView(),
            'absenceN' => $absenceN,
            'elevesabsence' => $eleves,
            'date' => $stringDate,
            'absences' => $absences,
            'heurActuel' => $heurActuel,
            // 'state' => $state,
        ]);

    }

    #[Route('/addAbsence' , name:'ajouterAbsence', methods:['POST'])]
    public function ajouterAbsence(Request $request, EntityManagerInterface $entityManager) : RedirectResponse
    {
        $absenceRepository = $entityManager->getRepository(Absence::class);
        $heureD = $request->request->get('heureD');
        $heureF = $request->request->get('heureF');
        $id = $request->request->get('id');
        $date = $request->request->get('date');
        $absence = $absenceRepository->addAbsence($id, $date, $heureD, $heureF);
        
        return $this->redirectToRoute('absence');
    }

}