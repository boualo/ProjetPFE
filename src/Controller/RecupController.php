<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\UpdatePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecupController extends AbstractController {

    #[Route('/mailUtilisateur', name:'prend_nom_user')]
    public function getMail() {
        return $this->render('mail/getmail.html.twig', []);
    }

    #[Route('/verifAlert', name:'verifier')]
    public function verifAlert(Request $request) {
        
        return $this->render('mail/verifAlert.html.twig');
    }

    #[Route('/motPasseRecuperation', name:'recup_mot_passe')]
    public function recupPassword(Request $req, EntityManagerInterface $entityManager) {
        $transport = Transport::fromDsn('smtp://salelfadiliwa@gmail.com:mrqxpcelovgdzayh@smtp.gmail.com:587');
        $mailer = new Mailer($transport);
        $userMail = $req->request->get('email');
        $userByMail = $entityManager->getRepository(Admin::class)->findByEmail($userMail);
        $id = $userByMail[0]['id'];
        $user = $entityManager->getRepository(Admin::class)->find($id);
        $email = (new Email())
            ->from('salelfadiliwa@gmail.com')
            ->to($userMail)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Récupération de mot de passe')
            ->text('Vous pouvez changer votre mot de passe avec le lien ci-dessous')
            ->html('<a class="alert alert-success" href="https://localhost:8000/mailForm/'.$id.'">Lien de Récupération de votre Mot de Passe</a>');

        $mailer->send($email);
        return $this->redirectToRoute('verifier');
        // return $this->json(["user" => $user[0]['id']]);
    }

    #[Route('/mailForm/{id}')]
    public function updatePw(int $id, Request $req, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager) {
        $admin = new Admin();
        $user = $entityManager->getRepository(Admin::class)->find($id);
        $form = $this->createForm(UpdatePasswordFormType::class, $user);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            if($req->get('newPwd') == $req->get('conNewPwd'))
            {
                $data = $form->getData();
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,$req->get('newPwd')
                    )
                );
                // $user->setPassword($data->getPassword());
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success','Le mot de passe a été modifié.');
                
                return $this->redirectToRoute('app_login');
            }
            else
            {
                $this->addFlash('danger','Les mots de passe ne sont pas identiques.');
            }

        }
        return $this->render('mail/updatePassword.html.twig', [
            'updateForm' => $form->createView(),
            'user' => $user,
            'id' => $id
        ]);
    }
}