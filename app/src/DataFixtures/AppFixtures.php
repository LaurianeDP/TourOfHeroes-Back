<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Generate fake data to populate the author table
        $listAuthor = [];
        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setFirstName("Prénom ".$i);
            $author->setLastName("Nom ".$i);
            $manager->persist($author);
            //The author created gets saved into an array
            $listAuthor[] = $author;
        }
        //Generate fake data to populate the book table
        for ($i = 0; $i < 20; $i++) {
            $book = new Book;
            $book->setTitle('Livre '.$i);
            $book->setCoverText('Quatrième de couverture numéro: '.$i);
            //The book is linked to a random author in the authors array
            $book->setAuthor($listAuthor[array_rand($listAuthor)]);
            $manager->persist($book);
        }

        $manager->flush();
    }
}
