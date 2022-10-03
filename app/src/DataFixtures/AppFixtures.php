<?php

namespace App\DataFixtures;

use App\Entity\Hero;
use App\Entity\Power;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    protected Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $powers = [];
        $this->faker = Factory::create('en_US');
        $maxLength = function($string) {
            return mb_strlen($string) <= 43;
        };

        for ($i = 0; $i < 15; $i++) {
            $power = (new Power())
                ->setName('Super '.$this->faker->colorName());

            $manager->persist($power);
            $powers[] = $power;
        }

        for ($i = 0; $i < 10; $i++) {
            $fakeName = $this->faker->valid($maxLength)->country();
            $hero = (new Hero())
                ->setName('Captain '.$fakeName)
                ->setPower($powers[array_rand($powers)])
                ->setAlterEgo($this->faker->name());

            $manager->persist($hero);
        }

        $manager->flush();
    }
}
