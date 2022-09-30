<?php

namespace App\Tests\Entity;

use App\Entity\Power;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helpers\ValidatorParser;

class PowerTest extends KernelTestCase
{

    public function testValidEntity()
    {
        //Creation of a power with a valid format
        $power = $this->getEntity();
        //Checks error returned, expects zero
        $this->assertHasErrors($power, 0);
    }

    public function testInvalidEntityName()
    {
        //Creation of a power with a valid format
        $power = $this->getEntity();

        //Changes name value to an invalid value
        $power->setName('');
        //Checks error returned, expects one, power name cannot be empty
        $this->assertHasErrors($power, 1);
    }

    public function getEntity(): Power
    {
        return $power = (new Power())
            ->setName('Hero power');
    }

    public function assertHasErrors(Power $power, int $errorCount = 0)
    {
        self::bootKernel();
        //Validate power created and store error if one is returned
        $error = self::getContainer()->get('validator')->validate($power);
        self::assertCount($errorCount, $error);
    }

}