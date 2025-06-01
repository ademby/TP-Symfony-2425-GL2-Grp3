<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product2 = new Product();
        $product3 = new Product();
        $product4 = new Product();

        $product1->setTitle("Porshe"     )->setDescription("Porshe Cayenne"     )->setPrice(5000000)->setProperties(['Moteur' => 'V8' , 'Boite Vitesse' => '6 Vitesses', 'Puissance' => '500 Cheveaux' ])->setImageURL("assets/images/porshe.jpg"     );
        $product2->setTitle("Ferrari"    )->setDescription("Ferrari 488"        )->setPrice(4000000)->setProperties(['Moteur' => 'V8' , 'Boite Vitesse' => '7 Vitesses', 'Puissance' => '600 Cheveaux' ])->setImageURL("assets/images/ferrari.jpg"    );
        $product3->setTitle("Lamborghini")->setDescription("Lamborghini Huracan")->setPrice(6000000)->setProperties(['Moteur' => 'V10', 'Boite Vitesse' => '7 Vitesses', 'Puissance' => '650 Cheveaux' ])->setImageURL("assets/images/lamborghini.jpg");
        $product4->setTitle("Bugatti"    )->setDescription("Bugatti Chiron"     )->setPrice(8000000)->setProperties(['Moteur' => 'W16', 'Boite Vitesse' => '7 Vitesses', 'Puissance' => '1500 Cheveaux'])->setImageURL("assets/images/bugatti.jpeg"   );

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);

        $manager->flush();
    }
}
