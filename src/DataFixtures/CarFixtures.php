<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category1 = new Category();
        $category2 = new Category();

        $category1->setName("Voiture de Luxe")->setImageURL('images/cat_Voiture de Luxe.jpg');
        $category2->setName("Voiture Sportive")->setImageURL('images/cat_Voiture Sportive.jpg');

        $manager->persist($category1);
        $manager->persist($category2);

        $manager->flush();

        $category1 = $manager->getRepository(Category::class)->findOneBy(['name' => 'Voiture de Luxe']);
        $category2 = $manager->getRepository(Category::class)->findOneBy(['name' => 'Voiture Sportive']);

        $product1 = new Product();
        $product2 = new Product();
        $product3 = new Product();
        $product4 = new Product();

        $product1->setTitle("Porshe")->setDescription("Porshe Cayenne")->setPrice(5000000)
            ->setProperties(['Moteur' => 'V8', 'Boite Vitesse' => '6 Vitesses', 'Puissance' => '500 Cheveaux'])
            ->setImageURL("images/porshe.jpg")->setCategory($category1);
        
        $product2->setTitle("Ferrari")->setDescription("Ferrari 488")->setPrice(4000000)
            ->setProperties(['Moteur' => 'V8', 'Boite Vitesse' => '7 Vitesses', 'Puissance' => '600 Cheveaux'])
            ->setImageURL("images/ferrari.jpg")->setCategory($category1);

        $product3->setTitle("Lamborghini")->setDescription("Lamborghini Huracan")->setPrice(6000000)
            ->setProperties(['Moteur' => 'V10', 'Boite Vitesse' => '7 Vitesses', 'Puissance' => '650 Cheveaux'])
            ->setImageURL("images/lamborghini.jpg")->setCategory($category2);

        $product4->setTitle("Bugatti")->setDescription("Bugatti Chiron")->setPrice(8000000)
            ->setProperties(['Moteur' => 'W16', 'Boite Vitesse' => '7 Vitesses', 'Puissance' => '1500 Cheveaux'])
            ->setImageURL("images/bugatti.jpeg")->setCategory($category2);

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);

        $manager->flush();
    }
}
