<?php

namespace App\Controller;

use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Articles::class)->findBy([], ['created_at' => 'DESC'], 6);

        return $this->render('main/index.html.twig', [
            'articles' => $articles
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
    #[Route('/service', name: 'app_main_about')]
    public function service(EntityManagerInterface $entityManager): Response
    {
        // $articles = $entityManager->getRepository(Articles::class)->findBy([], ['created_at' => 'DESC'], 6);

        return $this->render('main/service.html.twig', [
            // 'articles' => $articles
        ]);
    }
}
