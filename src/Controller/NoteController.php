<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Note;
use App\Repository\EleveRepository;
use App\Repository\FiliereRepository;
use App\Repository\GroupRepository;
use App\Repository\MatiereRepository;
use App\Repository\NivScolRepository;
use App\Repository\SousNiveauScolRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class NoteController extends AbstractController
{
    private $repoFiliere;
    private $repoNivScol;
    private $repoEleve;
    private $repoGroupe;
    private $repoSousNivScol;
    private $repoMatiere;
    private $doctrine;
    public function __construct(FiliereRepository $repoFiliere,NivScolRepository $repoNivScol,SousNiveauScolRepository $repoSousNivScol,EleveRepository $repoEleve,GroupRepository $repoGroupe,MatiereRepository $repoMatiere,ManagerRegistry $doctrine){
        $this->repoFiliere = $repoFiliere;
        $this->repoNivScol = $repoNivScol;
        $this->repoSousNivScol = $repoSousNivScol;
        $this->repoMatiere = $repoMatiere;
        $this->repoGroupe = $repoGroupe;
        $this->repoEleve = $repoEleve;
    }
    #[Route('/note', name: 'app_note')]
    public function index(): Response
    {
        $schoolLevels = $this->repoSousNivScol->findAll();
        return $this->render('note/index.html.twig', [
            'schoolLevels' => $schoolLevels,

        ]);
    }
   
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
    
    #[Route('/importFile',name:"import_data")]
    public function importData(Request $request,ManagerRegistry $doctrine,EntityManagerInterface $entityManager) :Response
    {
        $excelFile = $request->files->get('excel_file');

        if ($excelFile) {
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            $entityManager = $doctrine->getManager();
            $sousNivSco = $rows[0][2];
            $semester = $rows[1][2];
            $anneeScol = $rows[2][2];
            $groupe = $this->repoGroupe->find($rows[0][6]);
            $matiere = $this->repoMatiere->findOneBy(['nomMat'=>$rows[2][6]]);
            if($semester == "1ére semester")
                $semester=1;
            else 
                $semester=2;
            $date=new \DateTime();
            foreach (array_slice($rows, 6) as $row) {
                
                
                $eleve = $this->findByCodeMassar($row[0],$entityManager);
                $eleve = $this->repoEleve->findOneBy(['codeMassar'=>$row[0]]);
                $note = new Note();
                $note->setEleve($eleve); // Assuming the first column contains the product name
                $note->setMatiere($matiere); // Assuming the first column contains the product name
                if($row[4]!=null)
                    $note->setDevoire1($row[4]); // Assuming the second column contains the product price
                if($row[5]!=null)
                    $note->setDevoire2($row[5]); // Assuming the second column contains the product price
                if($row[6]!=null)
                    $note->setDevoire3($row[6]); // Assuming the second column contains the product price
                $note->setDateNote(new DateTime($date->format("Y-m-d"))); // Assuming the second column contains the product price
                if($row[7]!=null)
                    $note->setRemarque($row[7]); // Assuming the second column contains the product price
                $note->setSemester($semester); // Assuming the second column contains the product price

                // Add any other necessary data mapping

                $entityManager->persist($note);
            }

            $entityManager->flush();

            return $this->redirectToRoute('list_etudiant');
        }

        return $this->render('note/index.html.twig');
    }

    #[Route('/export' , name:'export_data')]
    public function exportData(Request $request,ManagerRegistry $doctrine): Response
    {
        $user=$this->getUser();
        // Fetch the data from the database
        $entityManager = $doctrine->getManager();
        $eleveRepository = $entityManager->getRepository(Eleve::class);
        $eleves = $eleveRepository->findAll();

        // Create a new Spreadsheet and set its properties
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Your Name')
            ->setLastModifiedBy('Your Name')
            ->setTitle('Eleve Data Export')
            ->setDescription('Data export from Eleve table');
        if($request->get('semester')==1)
            $semester = "1ére semester";
        else
            $semester = "2éme semester";
        
        $filiere = $this->repoFiliere->find($request->get('idFil'));
        $groupe = $this->repoGroupe->find($request->get('idGroup'));
        $sousNivSco = $this->repoSousNivScol->find($request->get('idSousNiveau'));
        // Add data to the worksheet
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'Niveau Scolaire')
                  ->setCellValue('C1', $sousNivSco->getNom())
                  ->setCellValue('A4', 'Semester')
                  ->setCellValue('B4', $semester)
                  ->setCellValue('A3', 'Année Scolaire')
                  ->setCellValue('C3', date('Y')."/".date('Y')+1)
                  ->setCellValue('A2', 'Filiére')
                  ->setCellValue('C2', $filiere->getNom())
                  ->setCellValue('E1', 'Classe')
                  ->setCellValue('G1', $groupe->getNomGroup())
                  ->setCellValue('E2', 'Enseignant')
                  ->setCellValue('G2', $user->getNom()." ".$user->getPrenom())
                  ->setCellValue('E3', 'Matiere')
                  ->setCellValue('G3', $user->getMatiere()->getNomMat())

                  ->setCellValue('A6', 'Code Massar')
                  ->setCellValue('C6', 'Nom complet')
                  ->setCellValue('E6', 'Devoir 1')
                  ->setCellValue('F6', 'Devoir 2')
                  ->setCellValue('G6', 'Devoir 3')
                  ->setCellValue('H6', 'Remarque')
                  ;
        $cellStyle = $worksheet->getStyle('A6');
        // Set the background color
        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('B6');
        // Set the background color
        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('C6');
        // Set the background color
        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('D6');
        // Set the background color
        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('E6');
        // Set the background color
        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('F6');

        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('G6');

        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $cellStyle = $worksheet->getStyle('H6');
        // Set the background color
        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $cellStyle->getFill()->getStartColor()->setARGB(Color::COLOR_CYAN);
        $row = 7;
        foreach ($eleves as $eleve) {
            $worksheet->setCellValue('A' . $row, $eleve->getCodeMassar())
                      ->setCellValue('C' . $row, $eleve->getNom()." ".$eleve->getPrenom())
                      ->setCellValue('E' . $row, "")
                      ->setCellValue('F' . $row, "")
                      ->setCellValue('G' . $row, "")
                      ->setCellValue('H' . $row, "");

            // Add more columns if needed

            $row++;
        }
        
       // Create a temporary file for the Excel data
       $temporaryFilePath = tempnam(sys_get_temp_dir(), 'note');
       $writer = new Xlsx($spreadsheet);
       $writer->save($temporaryFilePath);

       // Return the Excel file as a response to the user for download
       $response = new BinaryFileResponse($temporaryFilePath);
       $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'notes.xlsx');

       return $response;
    }
    

    public function findByCodeMassar($value,$entityManager)
   {
        $eleveRepository = $entityManager->getRepository(Eleve::class);

        $eleve = $eleveRepository->createQueryBuilder('e')
            ->select('e.id,e.codeMassar,e.dateNaissance,e.lieuNaissance,e.nom,e.prenom,e.adresse,e.tel,e.email')
            ->where('e.codeMassar= :role')
            ->setParameter('role',$value)
            ->getQuery()
            ->getResult();
        return $eleve;
   }
}
