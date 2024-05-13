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

    CategoryFixtures :
 class CategoryFixtures extends Fixture
 {
     public const CATEGORY_REFERENCE = 'category-ref';
     public function load(ObjectManager $manager): void
     {
 
         $categoriesData = json_decode(file_get_contents(__DIR__ . '/categoriesDatas.json'), true);
 
         $i = 0;
         foreach ($categoriesData as $categoryData) {
             $category = new Category();
             $category->setName($categoryData['name']);
             $manager->persist($category);
 
             $this->setReference(self::CATEGORY_REFERENCE . '-' . $i, $category);
 
             $i++;
         }
         $manager->flush();
     }
 }

 UserFixtures :
 class UserFixtures extends Fixture
 {
     public const USER_REFERENCE = "user-ref";
 
     public function __construct(
         private UserPasswordHasherInterface $hasher,
     ) {
     }
 
     public function load(ObjectManager $manager): void
     {
         $usersDatas = json_decode(file_get_contents(__DIR__ . '/usersDatas.json'), true);
 
 
         $i = 0;
         foreach ($usersDatas as $userAtt) {
             $user = $this->createUser(
                 $userAtt['firstName'],
                 $userAtt['lastName'],
                 $userAtt['email'],
                 $userAtt['password'],
             );
             $manager->persist($user);
 
             $this->addReference(self::USER_REFERENCE . '-' . $i, $user);
             $i++;
         }
         $manager->flush();
     }
     private function createUser(string $firstName, string $lastName, string $email, string $password): User
     {
         $user = new User();
         $user->setFirstname($firstName)
             ->setLastname($lastName)
             ->setEmail($email)
             ->setPassword($this->hasher->hashPassword($user, $password))
             ->setRoles(['ROLE_USER']) // rôle par défaut
             ->setCreatedAt(new \DateTimeImmutable())
             ->setUserUuid(Uuid::v4());
         $avatar = new Avatar();
         $avatar->setName('avatar');
         $avatar->setImageUrl('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
         $avatar->setPath('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
         $avatar->setAvatar('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
         $user->setAvatar($avatar);
 
         return $user;
     }
 }
 
 CommentFixtures :
 use App\Entity\Comment;
 use Doctrine\Common\DataFixtures\DependentFixtureInterface;
 use Doctrine\Bundle\FixturesBundle\Fixture;
 use Doctrine\Persistence\ObjectManager;
 
 
 class CommentFixtures extends Fixture implements DependentFixtureInterface
 
 
 {
     public const COMMENT_REFERENCE = 'comment-ref';
 
     public function load(ObjectManager $manager): void
     {
 
         $commentsData = json_decode(file_get_contents(__DIR__ . '/commentsDatas.json'), true);
 
         $usersDatas = json_decode(file_get_contents(__DIR__ . '/usersDatas.json'), true);
         $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);
 
         $numberOfUsers = count($usersDatas);
         $numberOfFigures = count($figuresData);
 
         foreach ($commentsData as $commentAttr) {
             $comment = new Comment();
             $comment->setContent($commentAttr['content'])
                       ->setCreatedAt(new \DateTimeImmutable());
 
             // Attribuer aléatoirement un utilisateur au commentaire
             $randomIndexUsers = rand(0, $numberOfUsers - 1);
             $randomUser = $this->getReference('user-ref-' . $randomIndexUsers);
             $comment->setUser($randomUser);
 
             // Attribuer aléatoirement une figure au commentaire
             $randomIndexFigures = rand(0, $numberOfFigures - 1);
             $randomFigure = $this->getReference('figure-ref-' . $randomIndexFigures);
             $comment->setFigure($randomFigure);
 
             $manager->persist($comment);
         }
 
         $manager->flush();
 
         // Référence unique pour les commentaires
         $this->addReference(self::COMMENT_REFERENCE, $comment);
     }
 
     public function getDependencies()
     {
         return [
             FigureFixtures::class,
         ];
     }
 }
