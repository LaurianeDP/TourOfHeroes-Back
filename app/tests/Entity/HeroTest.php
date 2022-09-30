<?php

namespace App\Tests\Entity;

use App\Entity\Hero;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helpers\ValidatorParser;

class HeroTest extends KernelTestCase
{

    public function testValidEntity()
    {
        //Creation of a hero with a valid format
        $hero = $this->getEntity();
        //Checks error returned, expects zero
        $this->assertHasErrors($hero, 0);

        //Creation of a hero with a valid format and empty alterEgo
        $hero->setAlterEgo(null);
        //Checks error returned, still expects zero
        $this->assertHasErrors($hero, 0);
    }

    public function testInvalidEntityName()
    {
        //Creation of a hero with a valid format
        $hero = $this->getEntity();

        //Changes name value to an invalid value
        $hero->setName('');
        //Checks error returned, expects two, hero name cannot be null, and must be over two characters long
        $this->assertHasErrors($hero, 2);

        $hero->setName('Checking if exceeding characters on hero name is working correctly');
        //Checks error returned, expects one, hero name cannot be over 50 characters long
        $this->assertHasErrors($hero, 1);

        $hero->setName('a');
        //Checks error returned, expects one, hero name cannot be under 2 characters long
        $this->assertHasErrors($hero, 1);
    }

    public function testInvalidEntityPower() {
        $hero = $this->getEntity();
        $hero->setPower('');
        //Checks error returned, expects one, hero power cannot be empty
        $this->assertHasErrors($hero, 1);
    }

    public function testInvalidEntityAlterEgo() {
        $hero = $this->getEntity();
        $hero->setAlterEgo('');
        //Checks error returned, expects one, hero alter ego cannot be under two characters long
        $this->assertHasErrors($hero, 1);

        $hero->setAlterEgo('Checking if exceeding characters on hero name is working correctly, total number of allowed characters being one hundred');
        //Checks error returned, expects one, hero alter ego cannot be under over a hundred characters long
        $this->assertHasErrors($hero, 1);
    }

    public function getEntity(): Hero
    {
        return $hero = (new Hero())
            ->setName('Hero name')
            //Type set as string TO BE CHANGED
            ->setPower('Hero power')
            ->setAlterEgo('Hero real name');
    }

    public function assertHasErrors(Hero $hero, int $errorCount = 0)
    {
        self::bootKernel();
        //Validate hero created and store error if one is returned
        $error = self::getContainer()->get('validator')->validate($hero);
        self::assertCount($errorCount, $error);
    }

}