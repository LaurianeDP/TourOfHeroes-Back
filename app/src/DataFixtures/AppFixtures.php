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


        $powers = [
            'Super smart',
            'Super fast',
            'Super hot',
            'Super chill',
            'Super flexible',
            'Super invisible',
            'Super lame',
            'Super explosive',
            'Super friendly',
            'Super nice',
            'Super swirly',
            'Super magnetic',
            'Super strechy',
            'Super great',
            'Super swag',
            'Super cool',
            'Super awesome',
            'Super super',
            'Super swift',
            'Super strong'
        ];
        foreach($powers as $power) {
        $powerObject = (new Power())
            ->setName($power);
        $manager->persist($powerObject);
        $powersObjects[] = $powerObject;
        }

        for ($i = 0; $i < 50; $i++) {
            $fakeName = $this->faker->valid($maxLength)->country();
            $hero = (new Hero())
                ->setName('Captain '.$fakeName)
                ->setPower($powersObjects[array_rand($powersObjects)])
                ->setAlterEgo($this->faker->name());

            $manager->persist($hero);
        }

        $manager->flush();
    }
}
