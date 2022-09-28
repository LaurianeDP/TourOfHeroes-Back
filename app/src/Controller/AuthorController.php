<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    public function __construct(
        protected AuthorRepository       $authorRepository,
        protected SerializerInterface    $serializer,
        protected BookRepository         $bookRepository,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface  $urlGenerator,

    )
    {
    }

    #[Route('/authors', name: 'authors', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'authors' => $this->authorRepository->getAuthorsForView()
        ]);
//        dump($view);
//        return $view;
    }

    //Delete
    #[Route('/api/authors/{id}', name: 'deleteAuthor', methods: ['DELETE'])]
    public function deleteAuthor(Author $author): JsonResponse
    {

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    //Create
    #[Route('/api/authors', name: 'createAuthor', methods: ['POST'])]
    public function createAuthor(Request $request): JsonResponse
    {
        $author = $this->serializer->deserialize($request->getContent(),
            Author::class, 'json');

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $jsonAuthor = $this->serializer->serialize($author, 'json', ['groups' => 'getAuthors']);

        $location = $this->urlGenerator->generate('detailAuthor', ['id' =>
            $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, ['location' =>
            $location], true);
    }

    //Modify
    #[Route('/api/authors/{id}', name: 'updateAuthor', methods: ['PUT'])]
    public function updateAuthor(Author $currentAuthor, Request $request): JsonResponse
    {

        $updatedAuthor = $this->serializer->deserialize($request->getContent(),
            Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>
                $currentAuthor]);

        $this->entityManager->persist($updatedAuthor);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    //SHOW ALL AUTHORS IN JSON
    #[Route('/api/authors', name: 'author', methods: ['GET'])]
    public function getAllAuthor(): JsonResponse
    {
        $authorList = $this->authorRepository->findAll();
        $jsonAuthorList = $this->serializer->serialize($authorList, 'json', ['groups' => 'getAuthors']);

        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //SHOW ONE AUTHOR IN JSON
    #[Route('/api/authors/{id}', name: 'detailAuthor', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAuthor(Author $author): JsonResponse
    {
        $jsonAuthor = $this->serializer->serialize($author, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonAuthor, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
