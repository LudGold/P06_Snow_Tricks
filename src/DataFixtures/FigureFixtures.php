<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);

        foreach ($figuresData as $key => $figureAttr) {
            $figure = new Figure();

            $figure->setName($figureAttr['name'])
                ->setDescription($figureAttr['description'])
                ->setCreatedAt(new \DateTimeImmutable())
                ->setSlug($this->slugify($figureAttr['name']))
                ->setCategory($figureAttr['category']);
                $this->addReference('figure_' . $key, $figure);
                // Récupérer l'ID ou l'UUID de l'auteur de la figure depuis les données JSON
        $authorReference = $figureAttr['authorReference'];

        // Récupérer l'utilisateur correspondant à la référence
        $author = $this->getReference($authorReference);

        // Vérifier si l'auteur existe
        if ($author) {
            // Définir l'auteur pour cette figure
            $figure->setAuthor($author);
        } else {
            // Si l'auteur n'existe pas, vous pouvez gérer cela en conséquence
        }

            // Ajouter des images à la figure
            foreach ($figureAttr['images'] as $imageUrl) {
                $image = new Image();
                $image->setImage($imageUrl);
                $figure->addImage($image);
            }

            // Ajouter des vidéos à la figure
            foreach ($figureAttr['videos'] as $videoUrl) {
                $video = new Video();
                $video->setVideo($videoUrl);
                $figure->addVideo($video);
            }

            // Ajouter une catégorie à la figure
            $category = $manager->getRepository(Category::class)->findOneBy(['name' => $figureAttr['category']]);
            if ($category) {
                $figure->setCategories($category);
            }

            $manager->persist($figure);
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        // Slugify the text
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
}
