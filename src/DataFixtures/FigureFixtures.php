<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\DataFixtures\CommentFixtures;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class FigureFixtures extends Fixture 
{
    public const FIGURE_REFERENCE = 'figure-ref';
    
    public function load(ObjectManager $manager): void
    {
        // Récupérer toutes les références de commentaires
        $comments = $this->getReference(CommentFixtures::COMMENT_REFERENCE);

        // Récupérer les utilisateurs et les catégories
        $allUsers = $manager->getRepository(User::class)->findAll();
        $allCategories = $manager->getRepository(Category::class)->findAll();

        // Charger les données des figures depuis un fichier JSON
        $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);

        foreach ($figuresData as $index => $figureAttr) {
            if (!empty($figureAttr['name'])) {
                $figure = new Figure();
                $image = new Image();
                $video = new Video();
                $image->setImageName('img');
                // $image->setImageFile('file');
                $video->setName('video');
                $video->setVideo('nomvideo');
                $video->setVideoId('urlvideo');
                $figure->setName($figureAttr['name'])
                    ->addImage($image)
                    ->addVideo($video)
                    ->setDescription($figureAttr['description'])
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setSlug($this->slugify($figureAttr['name']));
            } else {
                sprintf("Attention : un nom de figure est manquant ou vide.\n");
                continue;
            }

            // Attribuer un auteur et une catégorie de manière aléatoire
            $randomUser = $allUsers[array_rand($allUsers)];
            $randomCategory = $allCategories[array_rand($allCategories)];
            $figure->setAuthor($randomUser)
                   ->setCategory($randomCategory);
            $comments = $comments->toArray();
            // Ajouter tous les commentaires à cette figure
            $randomComment = $comments[array_rand($comments)];
            $figure->addComment($randomComment);

            $manager->persist($figure);
            $this->addReference(self::FIGURE_REFERENCE . '_' . $index, $figure);
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        // Slugify the text
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }

    public function getDependencies()
    {
        return [
            CommentFixtures::class,
        ];
    }
}
