<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 6; $i++) { 
            $product = new Product();
            $product->setName($this->faker->word());
            $product->setDescription($this->faker->realText());
            $product->setIsAvaible(mt_rand(0,1));
            $product->setPrice(mt_rand(1.30,200));
            $product->setQuantity(mt_rand(1,30));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
