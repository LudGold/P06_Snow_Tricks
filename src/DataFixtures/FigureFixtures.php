<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Comment;
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
        $allUsers = $manager->getRepository(User::class)->findAll();
        $allCategories = $manager->getRepository(Category::class)->findAll();
        $allComments = $manager->getRepository(Comment::class)->findAll();

        for ($i = 0; $this->hasReference(UserFixtures::USER_REFERENCE . '_' . $i); $i++) {
            $allUsers[] = $this->getReference(UserFixtures::USER_REFERENCE . '_' . $i);

        }

        for ($i = 0; $this->hasReference(CategoryFixtures::CATEGORY_REFERENCE . '_' . $i); $i++) {
            $allCategories[] = $this->getReference(CategoryFixtures::CATEGORY_REFERENCE . '_' . $i);
        }
        for ($i = 0; $this->hasReference(CommentFixtures::COMMENT_REFERENCE . '_' . $i); $i++) {
            $allCategories[] = $this->getReference(CommentFixtures::COMMENT_REFERENCE . '_' . $i);
        }
               

        $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);

        foreach ($figuresData as $index => $figureAttr) {
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
                $defaultUser = new User();
                $defaultUser->setEmail('default@example.com');
                $defaultUser->setPassword('default_password');
                $defaultUser->setFirstname('Default');
                $defaultUser->setLastname('User');
                $manager->persist($defaultUser);
                $figure->setAuthor($defaultUser);
            }

            if (!empty($allCategories)) {
                $randomCategory = $allCategories[array_rand($allCategories)];
                $figure->setCategory($randomCategory);
            } else {
                // Créer une catégorie par défaut
                $defaultCategory = new Category();
                $defaultCategory->setName('Default Category');
                $manager->persist($defaultCategory);
                $figure->setCategory($defaultCategory);
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
            $this->addReference(self::FIGURE_REFERENCE . '_' . $index, $figure);
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        // Slugify the text
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
   }
