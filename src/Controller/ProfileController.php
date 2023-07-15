<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Eleve;
use App\Form\EleveFormType;
use App\Repository\AdminRepository;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    private $eleveRepo;
    private $entityManager;

    public function __construct(EleveRepository $eleveRepo,EntityManagerInterface $entityManager){
        $this->eleveRepo = $eleveRepo;
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        
        return $this->render('profile/index.html.twig');
    }
}
