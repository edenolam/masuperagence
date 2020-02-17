<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PropertyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++){
            $property = new Property();
            $property->setTitle($faker->words(3, true));
            $property->setDescription($faker->sentences(3, true));
            $property->setSurface($faker->numberBetween(20, 350));
            $property->setRooms($faker->numberBetween(2, 10));
            $property->setBedrooms($faker->numberBetween(0,15));
            $property->setFloor($faker->numberBetween(0,15));
            $property->setPrice($faker->numberBetween(100000,15000000));
            $property->setHeat($faker->numberBetween(0,count(Property::HEAT) -1));
            $property->setCity($faker->city);
            $property->setAddress($faker->address);
            $property->setPostalCode($faker->postcode);
            $property->setSold(false);
            $manager->persist($property);
        }
        $manager->flush();
    }
}
