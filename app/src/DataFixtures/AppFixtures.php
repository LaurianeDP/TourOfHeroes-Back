<?php

namespace App\DataFixtures;

use App\Entity\Hero;
use App\Entity\Power;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected Generator $faker;
    private $userPasswordHasher;

    public function __construct(userPasswordHasherInterface $userPasswordHasher) {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername("HeroAdmin");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "adminPassword"));

        $manager->persist($admin);

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
