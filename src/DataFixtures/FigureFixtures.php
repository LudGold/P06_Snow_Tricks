<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FigureFixtures extends Fixture implements DependentFixtureInterface
{
    public const FIGURE_REFERENCE = 'figure-ref';

    public function load(ObjectManager $manager): void
    {
        $allUsers = [];
        $allCategories = [];
        $allComments = [];

        for ($i = 0; $this->hasReference(UserFixtures::USER_REFERENCE . '_' . $i); $i++) {
            $allUsers[] = $this->getReference(UserFixtures::USER_REFERENCE . '_' . $i);
        }

        for ($i = 0; $this->hasReference(CategoryFixtures::CATEGORY_REFERENCE . '_' . $i); $i++) {
            $allCategories[] = $this->getReference(CategoryFixtures::CATEGORY_REFERENCE . '_' . $i);
        }

        // Récupérer tous les commentaires
        $allComments = $manager->getRepository(Comment::class)->findAll();

        $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);

        foreach ($figuresData as $figureAttr) {
            if (!empty($figureAttr['name'])) {
                $figure = new Figure();
                $image = new Image();
                $video = new Video();
                $image->setName('img');
                $image->setImage('image');
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

            if (!empty($allUsers)) {
                $randomUser = $allUsers[array_rand($allUsers)];
                $figure->setAuthor($randomUser);
            } else {
                echo "Aucun utilisateur disponible pour assigner comme auteur à la figure.\n";
                // Gérer le cas où $allUsers est vide
            }

            if (!empty($allCategories)) {
                $randomCategory = $allCategories[array_rand($allCategories)];
                $figure->setCategory($randomCategory);
            } else {
                echo "Aucune catégorie disponible pour assigner à la figure.\n";
                // Gérer le cas où $allCategories est vide
            }

            if (!empty($allComments)) {
                // Sélectionner un nombre aléatoire de commentaires entre 1 et 3
                $randomComments = array_rand($allComments, rand(1, min(3, count($allComments))));
                foreach ($randomComments as $commentIndex) {
                    $randomCommentReference = $allComments[$commentIndex];
                    $figure->addComment($randomCommentReference);
                }
            } else {
                echo "Aucun commentaire disponible pour assigner à la figure.\n";
                // Gérer le cas où $allComments est vide
            }

            $manager->persist($figure);
            $this->addReference(self::FIGURE_REFERENCE . '_' . $i, $figure);
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        // Slugify the text
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,

        ];
    }
}
