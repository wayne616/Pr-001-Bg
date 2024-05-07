<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Articles::class)->findBy([], ['created_at' => 'DESC'], 6);

        $horaires = [
            ['date' => 'lundi', 'horaire' => '06:30 - 20:00'],
            ['date' => 'mardi', 'horaire' => '06:30 - 20:00'],
            ['date' => 'mercredi', 'horaire' => '06:30 - 20:00'],
            ['date' => 'jeudi', 'horaire' => '06:30 - 20:00'],
            ['date' => 'vendredi', 'horaire' => '06:30 - 20:00'],
            ['date' => 'samedi', 'horaire' => '06:30 - 20:00'],
            ['date' => 'dimanche', 'horaire' => '06:30 - 20:00'],
        ];


        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            'horaires' =>$horaires,
        ]);
    }

    #[Route('/about', name: 'app_main_about')]
    public function about(EntityManagerInterface $entityManager): Response
    {
        // $articles = $entityManager->getRepository(Articles::class)->findBy([], ['created_at' => 'DESC'], 6);

        return $this->render('main/about.html.twig', [
            // 'articles' => $articles
        ]);
    }

    #[Route('/service', name: 'app_main_service')]
    public function service(EntityManagerInterface $entityManager): Response
    {
        // $articles = $entityManager->getRepository(Articles::class)->findBy([], ['created_at' => 'DESC'], 6);

        return $this->render('main/service.html.twig', [
            // 'articles' => $articles
        ]);
    }

    #[Route('/checkout', name: 'checkout')]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();
    
        // Récupérer les articles du panier envoyés depuis le frontend
        $cartItems = json_decode($request->getContent(), true);
    
        // Enregistrer chaque article du panier en tant qu'entité Order
        foreach ($cartItems as $cartItem) {
            $order = new Order();
            $order->setArticleId($cartItem['articleId']);
            $order->setUser($user);
            $order->setQuantity($cartItem['quantity']);
            $order->setRef(uniqid()); // Générez une référence unique pour chaque commande
    
            $entityManager->persist($order);
        }
    
        // Flusher les entités Order dans la base de données
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }
}
