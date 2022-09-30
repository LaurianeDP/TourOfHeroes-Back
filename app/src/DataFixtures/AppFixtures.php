<?php

namespace App\DataFixtures;

use App\Entity\Hero;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    protected Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create('en_US');

        for ($i = 0; $i < 10; $i++) {
            $hero = (new Hero())
                ->setName('Captain '.$this->faker->country())
                ->setPower('Super '.$this->faker->colorName())
                ->setAlterEgo($this->faker->name());

            $manager->persist($hero);
        }

        $manager->flush();
    }
}
