<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CatgeoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $categoriesData = json_decode(file_get_contents(__DIR__ . '/CategoriesDatas.json'), true);

        foreach ($categoriesData as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
