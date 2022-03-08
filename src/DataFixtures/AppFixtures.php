<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sushisCategory = ['Sushi', 'Sashimi', 'Maki', 'California', 'Menu', 'Boisson', 'Dessert'];
        $productNames = ['sushi crevette', 'sushi avocat', 'sushi thon', 'sushi saumon',
                        'sashimi thon', 'sashimi saumon', 'sashimi mixte',
                        'maki saumon', 'maki avocat', 'maki thon', 'maki concombre',
                        'california saumon avocat', 'california thon avocat', 'california tempura avocat', 'california crevette avocat', 'california concombre avocat',
                        'full saumon', 'full veggie', 'full thon','full crevette', 'full mixte', 'full full',
                        'sake', 'coca', 'asahi', 'coedo shiro', 'orangina', 'evian', 'badoit', 'ice-tea',
                        'mochi coco', 'mochi mangue', 'tiramisu café', 'tiramisu chocolat' ,'sweet sushi mangue anette'];
        $prices = [500, 400, 490, 450, 1290, 1190, 1290, 590, 590, 590, 590, 650, 650, 790, 650, 650, 1690, 1490, 1750, 1650, 1550, 30090, 990, 270, 390, 490, 250, 270, 270, 250, 270, 270, 450, 450, 270];

        foreach ($sushisCategory as $catName) {
            $cat = new Category();
            $cat->setName($catName);
            $manager->persist($cat);
            $manager->flush();
        }

        for ($i = 0; $i < count($prices); $i++) {
            $product = new Product();
            $product->setName($productNames[$i]);
            $product->setPrice($prices[$i]);
            $manager->persist($product);
            $manager->flush();
        }
    }
}



