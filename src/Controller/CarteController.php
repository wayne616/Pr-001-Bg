<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarteController extends AbstractController
{

    private $imagesDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->imagesDirectory = $parameterBag->get('images_directory');
    }
    
    #[Route('/carte', name: 'app_carte')]
    public function index(EntityManagerInterface $entityManager, Request $request,SluggerInterface $slugger): Response
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
    
            // Gérer le téléchargement de l'image s'il est disponible
            $imageFile = $form['image']->getData();
            if ($imageFile instanceof UploadedFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Utiliser le service Slugger pour générer un nom de fichier sécurisé
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                // Déplacer le fichier dans le répertoire où Webpack peut le trouver
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Répertoire de destination
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur lors du téléchargement
                    $this->addFlash('error', "Problème lors du chargement de l'image !");
                }
    
                // Mettre à jour la propriété image de l'article avec le nom de fichier
                $article->setImage($newFilename);
            }
    
            // Persister l'entité en base de données
            $entityManager->persist($article);
            $entityManager->flush();
    
            // Rediriger vers la page actuelle pour rafraîchir les données
            $this->addFlash('success', 'Pâtisserie ajouter avec success !');
            return $this->redirectToRoute('app_carte');
        }

        return $this->render('carte/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView(), // Passer la vue du formulaire au template Twig
        ]);
    }

    #[Route('/delete/{id}', name: 'app_carte_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $articles, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$articles->getId(), $request->request->get('_token'))) {
            // Supprimer l'image du système de fichiers
            $imagePath = $this->imagesDirectory . '/' . $articles->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $entityManager->remove($articles);
            $entityManager->flush();
            $this->addFlash('success', 'Pâtisserie supprimée avec succès !');

        } else {
            $this->addFlash('error', 'Problème lors de la suppression !');
        }

        return $this->redirectToRoute('app_carte', [], Response::HTTP_SEE_OTHER);
    }
}
