<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        try {
            if ($request->isMethod('POST')) {
                $nom = $request->request->get('nom');
                $prenom = $request->request->get('prenom');
                $email = $request->request->get('email');
                $tel = $request->request->get('tel');
                $objet = $request->request->get('objet');
                $message = $request->request->get('message');

                $email = (new Email())
                    ->from($email)
                    ->to('gagno@devmax.tech')
                    ->subject($objet)
                    ->text($message)
                    ->html("Nom: $nom <br> Prénom: $prenom <br> Email: $email <br> Tel: $tel <br> Objet: $objet <br> Message: $message");

                $mailer->send($email);

                $this->addFlash('success', "Email envoyée avec succès !");
                return $this->redirectToRoute('app_contact');
            }
        } catch (\Throwable $th) {
            $this->addFlash('error', "Erreur pendant l'envoi du message !");
        }

        return $this->render('contact/index.html.twig');
    }
}
