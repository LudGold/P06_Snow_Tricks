<?php

namespace App\DataFixtures;

use App\Entity\Figure;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Video;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;


class FigureFixtures extends Fixture implements DependentFixtureInterface
{
    public const FIGURE_REFERENCE = 'figure-ref';

             // Récupérer toutes les références de commentaires
         // $comments = $this->getReference(CommentFixtures::COMMENT_REFERENCE);
 
         // Récupérer les utilisateurs et les catégories
         public function load(ObjectManager $manager)
         {
         // Charger les données des figures depuis un fichier JSON
         $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);
         $usersDatas = json_decode(file_get_contents(__DIR__ . '/usersDatas.json'), true);
         $categoryDatas = json_decode(file_get_contents(__DIR__ . '/categoriesDatas.json'), true);
         $numberOfUsers = count($usersDatas);
         $numberOfCategories = count($categoryDatas);
 
         $i = 0;
         foreach ($figuresData as $figureAttr) {
                 $figure = new Figure();
                 $image = new Image();
                 $video = new Video();
                 $image->setImageName('img');
                 $image->setPath('file');
                 $video->setName('video');
                 $video->setVideoId('urlvideo');
                 $figure->setName($figureAttr['name'])
                     ->addImage($image)
                     ->addVideo($video)
                     ->setDescription($figureAttr['description'])
                     ->setCreatedAt(new \DateTimeImmutable())
                     ->setSlug($this->slugify($figureAttr['name']));
 
 
             // Attribuer un auteur et une catégorie de manière aléatoire
             $randomIndexUser = rand(0, $numberOfUsers - 1);
             $randomUser = $this->getReference('user-ref-' . $randomIndexUser);
 
             $randomIndexCategory = rand(0, $numberOfCategories - 1);
             $randomCategory = $this->getReference('category-ref-' . $randomIndexCategory);
 
             $figure->setAuthor($randomUser)
                 ->setCategory($randomCategory);
      
             $manager->persist($figure);
 
             $this->addReference(self::FIGURE_REFERENCE . '-' . $i, $figure);
 
             $i++;
         }
 
         $manager->flush();
     }
 
     private function slugify(string $text): string
     {
         // Slugify the text
         $slugger = new AsciiSlugger();
 
         return strtolower($slugger->slug($text));
     }
 
     public function getDependencies()
     {
         return [
             UserFixtures::class,
             CategoryFixtures::class,
         ];
     }
 
    }
