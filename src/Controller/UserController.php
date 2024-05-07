<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Security $security, Request $request): Response
    {
        $user = $security->getUser(); // Récupérer l'utilisateur connecté
    
        // Paramètres de pagination
        $page = $request->query->getInt('page', 1); // Récupérer le numéro de la page à partir de la requête, par défaut 1
        $limit = 6; // Nombre d'éléments par page
    
        // Récupérer le nombre total d'éléments
        $totalOrders = count($entityManager->getRepository(Order::class)->findBy(['user' => $user]));
    
        // Calculer le décalage (offset) en fonction du numéro de page
        $offset = ($page - 1) * $limit;
    
        // Récupérer les commandes de l'utilisateur avec la pagination
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user], ['id' => 'DESC'], $limit, $offset);
    
        // Initialiser un tableau pour stocker les commandes groupées par référence
        $groupedOrders = [];
    
        foreach ($orders as $order) {
            $ref = $order->getRef();
    
            // Vérifier si la référence existe déjà dans le tableau groupé
            if (!isset($groupedOrders[$ref])) {
                // Si la référence n'existe pas, initialisez un tableau vide pour cette référence
                $groupedOrders[$ref] = [];
            }
    
            // Ajouter la commande au tableau groupé sous la référence correspondante
            $groupedOrders[$ref][] = $order;
        }
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'orders' => $groupedOrders,
            'totalOrders' => $totalOrders,
            'currentPage' => $page,
            'perPage' => $limit
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete')]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        try {
            if ($this->isCsrfTokenValid('delete_profile'.$user->getId(), $request->request->get('_token'))) {
    
                // Remove the articles entity
                $entityManager->remove($user);
                $entityManager->flush();
            }
    
            $this->addFlash('success', 'User supprimé avec succès');
            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            // Gérer l'exception ici, par exemple, renvoyer une réponse d'erreur JSON
            $this->addFlash('error', "Error lors de la suppression");
            return new JsonResponse(['error' => $e->getMessage()], 500); // Code 500 pour une erreur interne du serveur
        }

    }
}
