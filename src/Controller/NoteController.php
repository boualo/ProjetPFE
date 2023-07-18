<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Note;
use App\Repository\FiliereRepository;
use App\Repository\GroupRepository;
use App\Repository\NivScolRepository;
use App\Repository\SousNiveauScolRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
    public function importData(Request $request,ManagerRegistry $doctrine): Response
    {
        $excelFile = $request->files->get('excel_file');

        if ($excelFile) {
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            $entityManager = $doctrine->getManager();
            dd($rows);
            // foreach ($rows as $row) {
            //     $product = new Note();
            //     $product->setNom($row[0]); // Assuming the first column contains the product name
            //     $product->setPrenom($row[1]); // Assuming the second column contains the product price
            //     $product->setTele($row[2]); // Assuming the second column contains the product price

            //     // Add any other necessary data mapping

            //     $entityManager->persist($product);
            // }

            $entityManager->flush();

            return $this->redirectToRoute('list_etudiant');
        }

        return $this->render('note/index.html.twig');
    }

    #[Route('/export' , name:'export_data')]
    public function exportData(ManagerRegistry $doctrine): Response
    {
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

        // Add data to the worksheet
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'ID')
                  ->setCellValue('B1', 'Name')
                  ->setCellValue('C1', 'Age');

        $row = 2;
        foreach ($eleves as $eleve) {
            $worksheet->setCellValue('A' . $row, $eleve->getId())
                      ->setCellValue('B' . $row, $eleve->getNom())
                      ->setCellValue('C' . $row, $eleve->getCodeMassar());

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
    
}
