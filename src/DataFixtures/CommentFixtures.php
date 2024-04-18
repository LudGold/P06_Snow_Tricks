<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $commentsData = json_decode(file_get_contents(__DIR__ . '/commentsDatas.json'), true);

        foreach ($commentsData as $commentAttr) {
            $comment = new Comment();
            $comment->setContent($commentAttr['content'])
                    ->setCreatedAt(new \DateTimeImmutable());
            // Recherche de la figure correspondante par son nom
            $figureName = $commentAttr['figure'];
            $figure = $manager->getRepository(Figure::class)->findOneBy(['name' => $figureName]);

            if ($figure) {
                $comment->setFigure($figure);
            } else {
                echo "La figure '$figureName' n'a pas été trouvée.\n";
            }

            $manager->persist($comment);
        }
        $manager->flush();
    }
}
