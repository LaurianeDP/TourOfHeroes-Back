<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\User;
use App\Factory\BookFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher) {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $data = BookFactory::createBook("Building Restful APIs", "test CovertText");
        $manager->persist($data);
        $manager->flush();

        //Generate fake data to populate the user table
        $user = new User();
        $user->setEmail("user@fakebookemail.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        //Generate fake data to populate the user admin table
        $userAdmin = new User();
        $userAdmin->setEmail("admin@fakebookemail.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);

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
