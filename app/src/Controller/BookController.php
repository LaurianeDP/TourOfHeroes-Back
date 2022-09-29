<?php

namespace App\Controller;

use App\Helpers\ValidatorParser;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    public function __construct(
        protected BookRepository         $bookRepository,
        protected SerializerInterface    $serializer,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface  $urlGenerator,
        protected AuthorRepository       $authorRepository,
        protected ValidatorInterface     $validator,
        protected ValidatorParser        $validatorParser,
//        protected Request                $request,
    )
    {
    }

    //SHOW ALL BOOKS IN JSON
    #[Route('/api/books', name: 'author', methods: ['GET'])]
    public function getAllBooks(): JsonResponse
    {
        $bookList = $this->bookRepository->findAll();
        $jsonBookList = $this->serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBookList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //SHOW ONE BOOK IN JSON
    #[Route('/api/books/{id}', name: 'detailBook', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getDetailBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //DELETE
    #[Route('/api/books/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book): JsonResponse
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    //CREATE
    #[Route('/api/books', name: 'createBook', methods: ['POST'])]
    public function createBook(Request $request): JsonResponse
    {
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');

        //Checks for errors when receiving the body of the request
        $errors = $this->validator->validate($book);
        $errors = $this->validatorParser->handleViolationList($errors);

        if (!empty($errors)) {
//            dump($errors->get(1));
            return new JsonResponse($this->serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        //All of the request, in the form of an array
        $content = $request->toArray();

        //If idAuthor is not in the request, sets its value at -1
        $idAuthor = $content['idAuthor'] ?? -1;

        //If the author is found, set it in the book's property, if not found, automatically
        // null
        $book->setAuthor($this->authorRepository->find($idAuthor));

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $jsonBook = $this->serializer->serialize($book, 'json', ['groups' => 'getBooks']);

        $location = $this->urlGenerator->generate('detailBook', ['id' => $book->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ['location' => $location],
            true);
    }

    //UPDATE
    #[Route('/api/books/{id}', name: 'updateBook', methods: ['PUT'])]
    public function updateBook(Request $request, Book $currentBook): JsonResponse
    {
        $updatedBook = $this->serializer->deserialize($request->getContent(),
            Book::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);

        $content = $request->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;
        $updatedBook->setAuthor($this->authorRepository->find($idAuthor));

        $this->entityManager->persist($updatedBook);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    //SHOW ALL BOOKS IN HTML
    #[Route('/books', name: 'book', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $this->bookRepository->findAll()
        ]);
//        dump($view);
    }
}
