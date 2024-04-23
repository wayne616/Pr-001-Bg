<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création de 10 articles de boulangerie
        for ($i = 1; $i <= 10; $i++) {
            $article = new Articles();
            $article->setName("Article $i");
            $article->setPrice(mt_rand(1, 10)); // Prix aléatoire entre 1 et 10
            $article->setDescription("Description de l'article $i");
            
            $manager->persist($article);
        }

        $manager->flush();
    }
}
