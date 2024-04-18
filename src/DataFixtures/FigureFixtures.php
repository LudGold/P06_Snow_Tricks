<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();
        $figure = new Figure();
        $figure
            ->setName('Nom de la figure')
            ->setDescription('Description de la figure')
            ->setCategory('Catégorie de la figure')
            ->setSlug(strtolower($slugger->slug($figure->getName())));

        $manager->persist($figure);

        $manager->flush();
    }
}
