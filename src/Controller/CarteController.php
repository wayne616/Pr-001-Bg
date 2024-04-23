<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarteController extends AbstractController
{
    #[Route('/carte', name: 'app_carte')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $articles = $entityManager->getRepository(Articles::class)->findAll();

        // Créer une nouvelle instance d'Articles
        $article = new Articles();

        // Créer un formulaire pour l'entité Articles
        $form = $this->createForm(ArticlesFormType::class, $article);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire soumis
            $article = $form->getData();

            // Persister l'entité en base de données
            $entityManager->persist($article);
            $entityManager->flush();

            // Rediriger vers la page actuelle pour rafraîchir les données
            return $this->redirectToRoute('app_carte');
        }

        return $this->render('carte/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView(), // Passer la vue du formulaire au template Twig
        ]);
    }
    
    #[Route('/carte/new', name: 'app_carte_new')]
    public function new(): Response
    {
        return $this->render('carte/new.html.twig', [
            'controller_name' => 'CarteController',
        ]);
    }
}
